<?php

namespace App\Http\Middleware;

use App\Models\Api\ApiClient;
use App\Models\Api\ApiRequestLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LogApiRequest
{
    private const SENSITIVE_FIELDS = [
        'password',
        'password_confirmation',
        'token',
        'access_token',
        'api_key',
        'authorization',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = hrtime(true);

        try {
            $response = $next($request);

            $this->writeLog(
                request: $request,
                response: $response,
                startedAt: $startedAt,
            );

            return $response;
        } catch (Throwable $e) {
            $this->writeLog(
                request: $request,
                response: null,
                startedAt: $startedAt,
                exception: $e,
            );

            throw $e;
        }
    }

    private function writeLog(Request $request, ?Response $response, int $startedAt, ?Throwable $exception = null): void
    {
        try {
            $user = $request->user();

            ApiRequestLog::query()->create([
                'api_client_id' => $user instanceof ApiClient
                    ? $user->id
                    : null,

                'method' => $request->method(),

                'endpoint' => '/' . ltrim(
                        $request->path(),
                        '/'
                    ),

                'route_name' => $request->route()?->getName(),

                'request_ip' => $request->ip(),

                'user_agent' => $request->userAgent(),

                'request_data' => $this->sanitize(
                    $request->all()
                ),

                'response_code' => $response?->getStatusCode()
                    ?? 500,

                'response_data' => $response
                    ? $this->getResponseData($response)
                    : null,

                'error_message' => $exception?->getMessage(),

                'execution_time_ms' => (int) round(
                    (hrtime(true) - $startedAt) / 1_000_000
                ),

                'created_at' => now(),
            ]);
        } catch (Throwable $e) {
            // Ошибка логирования не должна ломать API.
            report($e);
        }
    }

    private function sanitize(array $data): array
    {
        foreach ($data as $key => $value) {
            if (
                in_array(
                    strtolower((string) $key),
                    self::SENSITIVE_FIELDS,
                    true
                )
            ) {
                $data[$key] = '[REDACTED]';

                continue;
            }

            if (is_array($value)) {
                $data[$key] = $this->sanitize($value);
            }
        }

        return $data;
    }

    private function getResponseData(Response $response): ?array
    {
        $contentType = $response->headers->get('Content-Type');

        if (
            ! $contentType
            || ! str_contains(
                strtolower($contentType),
                'application/json'
            )
        ) {
            return null;
        }

        $content = $response->getContent();

        if (! is_string($content) || $content === '') {
            return null;
        }

        $data = json_decode(
            $content,
            true
        );

        return is_array($data)
            ? $this->sanitize($data)
            : null;
    }
}
