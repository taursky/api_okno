<?php

namespace App\Console\Commands;

use App\Models\Api\ApiClient;
use Illuminate\Console\Command;

class ListApiClientTokens extends Command
{
    protected $signature = 'api:client:tokens {id : API client ID}';

    protected $description = 'List access tokens for API client';

    public function handle(): int
    {
        $client = ApiClient::query()
            ->with('tokens')
            ->find($this->argument('id'));

        if (! $client) {
            $this->error('API client not found.');

            return self::FAILURE;
        }

        $this->info(
            "API client: {$client->name}"
        );

        if ($client->tokens->isEmpty()) {
            $this->warn('No tokens found.');

            return self::SUCCESS;
        }

        $this->table(
            [
                'ID',
                'Name',
                'Abilities',
                'Last used',
                'Expires',
                'Created',
            ],
            $client->tokens->map(function ($token) {
                return [
                    $token->id,
                    $token->name,
                    implode(', ', $token->abilities ?? []),
                    $token->last_used_at?->format('Y-m-d H:i:s') ?? '-',
                    $token->expires_at?->format('Y-m-d H:i:s') ?? 'Never',
                    $token->created_at?->format('Y-m-d H:i:s') ?? '-',
                ];
            })->all()
        );

        return self::SUCCESS;
    }
}
