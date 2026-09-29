<?php

namespace App\Jobs\Crm;

use App\Models\External\CrmWebhookEvent;
use App\Services\Crm\CrmWebhookRequestBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendCrmWebhook implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 5;
    public int $timeout = 15;

    public function __construct(public readonly int $webhookEventId)
    {
        $this->onQueue(
            config('services.crm.webhook_queue', 'crm-webhooks')
        );
    }

    public function handle(CrmWebhookRequestBuilder $crm): void
    {
        $webhook = CrmWebhookEvent::query()->findOrFail($this->webhookEventId);

        if ($webhook->status === CrmWebhookEvent::STATUS_DELIVERED) {
            return;
        }

        if ($webhook->status === CrmWebhookEvent::STATUS_FAILED) {
            return;
        }

        $webhook->increment('attempts');
        $webhook->refresh();

        $webhook->update([
            'status' => CrmWebhookEvent::STATUS_PROCESSING,
            'last_error' => null,
            'next_attempt_at' => null,
        ]);

        /*
         * Ровно тот JSON, который был сохранён при создании события.
         */
        $body = $webhook->payload;

        /*
         * URL зависит от event_type,
         * timestamp/signature генерируются заново для каждой попытки.
         */
        $url = $crm->url($webhook->event_type);
        $headers = $crm->headers($body);

        Log::info('CRM webhook request', [
            'webhook_id' => $webhook->id,
            'event_id' => $webhook->event_id,
            'event_type' => $webhook->event_type,
            'attempt' => $webhook->attempts,
            'url' => $url,
            'method' => 'POST',
            'headers' => $headers,
            'body' => $body,
        ]);

        try {
            $response = Http::withHeaders($headers)
                ->connectTimeout(config('services.crm.connect_timeout', 3))
                ->timeout(config('services.crm.timeout', 5))
                ->withBody($body, 'application/json')
                ->post($url);
        } catch (ConnectionException $exception) {
            $this->retryWebhook(
                $webhook,
                'Connection error: ' . $exception->getMessage()
            );

            return;
        } catch (Throwable $exception) {
            $this->retryWebhook(
                $webhook,
                'Request error: ' . $exception->getMessage()
            );

            return;
        }

        $status = $response->status();

        Log::info('CRM webhook response', [
            'webhook_id' => $webhook->id,
            'event_id' => $webhook->event_id,
            'event_type' => $webhook->event_type,
            'attempt' => $webhook->attempts,
            'status' => $status,
            'headers' => $response->headers(),
            'body' => $response->body(),
        ]);

        $webhook->update([
            'last_http_status' => $status,
            'last_response' => $response->body(),
        ]);

        if (in_array($status, [200, 202], true)) {
            $webhook->update([
                'status' => CrmWebhookEvent::STATUS_DELIVERED,
                'delivered_at' => now(),
                'next_attempt_at' => null,
                'last_error' => null,
            ]);

            return;
        }

        $retryAfter = $this->retryAfterSeconds(
            $response->header('Retry-After')
        );

        $this->retryWebhook(
            $webhook,
            "CRM returned HTTP {$status}",
            $retryAfter
        );
    }

    private function retryWebhook(
        CrmWebhookEvent $webhook,
        string $error,
        ?int $retryAfter = null
    ): void {
        /*
         * Последняя разрешённая попытка.
         * Больше Job в очередь не возвращаем.
         */
        if ($webhook->attempts >= $this->tries) {
            $webhook->update([
                'status' => CrmWebhookEvent::STATUS_FAILED,
                'last_error' => $error,
                'next_attempt_at' => null,
            ]);

            Log::error('CRM webhook permanently failed', [
                'webhook_id' => $webhook->id,
                'event_id' => $webhook->event_id,
                'event_type' => $webhook->event_type,
                'entity_type' => $webhook->entity_type,
                'entity_id' => $webhook->entity_id,
                'attempts' => $webhook->attempts,
                'last_http_status' => $webhook->last_http_status,
                'last_response' => $webhook->last_response,
                'error' => $error,
                'payload' => $webhook->payload,
            ]);

            return;
        }

        $delay = $retryAfter ?? $this->delayForAttempt(
            $webhook->attempts
        );

        $webhook->update([
            'status' => CrmWebhookEvent::STATUS_RETRY,
            'last_error' => $error,
            'next_attempt_at' => now()->addSeconds($delay),
        ]);

        Log::warning('CRM webhook scheduled for retry', [
            'webhook_id' => $webhook->id,
            'event_id' => $webhook->event_id,
            'event_type' => $webhook->event_type,
            'attempt' => $webhook->attempts,
            'retry_after' => $delay,
            'error' => $error,
        ]);

        $this->release($delay);
    }

    private function delayForAttempt(int $attempt): int
    {
        return match ($attempt) {
            1 => 5,
            2 => 30,
            3 => 120,
            default => 600,
        };
    }

    private function retryAfterSeconds(?string $value): ?int
    {
        if (!$value) {
            return null;
        }

        if (is_numeric($value)) {
            return max(1, (int) $value);
        }

        $timestamp = strtotime($value);

        if ($timestamp === false) {
            return null;
        }

        return max(1, $timestamp - time());
    }

    public function failed(Throwable $exception): void
    {
        $webhook = CrmWebhookEvent::query()->find($this->webhookEventId);

        if (!$webhook) {
            return;
        }

        if ($webhook->status === CrmWebhookEvent::STATUS_DELIVERED) {
            return;
        }

        $webhook->update([
            'status' => CrmWebhookEvent::STATUS_FAILED,
            'last_error' => mb_substr($exception->getMessage(), 0, 5000),
            'next_attempt_at' => null,
        ]);

        Log::error('CRM webhook job failed', [
            'webhook_id' => $webhook->id,
            'event_id' => $webhook->event_id,
            'event_type' => $webhook->event_type,
            'attempts' => $webhook->attempts,
            'error' => $exception->getMessage(),
            'payload' => $webhook->payload,
        ]);
    }
}
