<?php

namespace App\Console\Commands;

use App\Jobs\Crm\SendCrmWebhook;
use App\Models\External\CrmWebhookEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class DispatchPendingCrmWebhooks extends Command
{
    protected $signature = 'crm:webhooks:dispatch
                            {--sleep=1 : Sleep between checks}
                            {--limit=50 : Events per batch}
                            {--once : Process one batch and exit}';

    protected $description = 'Dispatch pending CRM webhook events to Laravel queue';

    public function handle(): int
    {
        do {
            $ids = $this->claimPendingEvents((int) $this->option('limit'));

            foreach ($ids as $id) {
                try {
//                    SendCrmWebhook::dispatch($id)->onQueue(config('services.crm.webhook_queue','crm-webhooks'));
                    SendCrmWebhook::dispatch($id);
                } catch (Throwable $exception) {
                    CrmWebhookEvent::query()
                        ->whereKey($id)
                        ->update([
                            'status' => CrmWebhookEvent::STATUS_PENDING,
                            'queued_at' => null,
                            'last_error' => $exception->getMessage(),
                        ]);
                }
            }

            if ($this->option('once')) {
                break;
            }

            sleep(max(1, (int) $this->option('sleep')));
        } while (true);

        return self::SUCCESS;
    }

    private function claimPendingEvents(int $limit): array
    {
        return DB::connection('external')->transaction(function () use ($limit) {

            /*
             * Также возвращаем "застрявшие" queued,
             * если dispatcher умер между claim и dispatch.
             */
            CrmWebhookEvent::query()
                ->where('status', CrmWebhookEvent::STATUS_QUEUED)
                ->where('queued_at', '<', now()->subMinutes(config('services.crm.queued_stale_minutes', 15)))
                ->update([
                    'status' => CrmWebhookEvent::STATUS_PENDING,
                    'queued_at' => null,
                ]);

            $events = CrmWebhookEvent::query()
                ->where('status', CrmWebhookEvent::STATUS_PENDING)
                ->whereNull('queued_at')
                ->orderBy('id')
                ->limit($limit)
                ->lockForUpdate()
                ->get();

            $ids = $events->pluck('id')->all();

            if ($ids) {
                CrmWebhookEvent::query()
                    ->whereIn('id', $ids)
                    ->update([
                        'status' => CrmWebhookEvent::STATUS_QUEUED,
                        'queued_at' => now(),
                    ]);
            }

            CrmWebhookEvent::query()
                ->where('status', CrmWebhookEvent::STATUS_PROCESSING)
                ->where('updated_at', '<', now()->subMinutes(
                    config('services.crm.processing_stale_minutes', 15)
                ))
                ->update([
                    'status' => CrmWebhookEvent::STATUS_PENDING,
                    'queued_at' => null,
                    'next_attempt_at' => null,
                    'last_error' => 'Recovered stale processing webhook',
                ]);

            return $ids;
        });
    }
}
