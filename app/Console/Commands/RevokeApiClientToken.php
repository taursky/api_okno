<?php

namespace App\Console\Commands;

use App\Models\Api\ApiClient;
use Illuminate\Console\Command;

class RevokeApiClientToken extends Command
{
    protected $signature = 'api:client:revoke
                            {id : API client ID}
                            {tokenId? : Token ID}
                            {--all : Revoke all tokens}';

    protected $description = 'Revoke API client access token';

    public function handle(): int
    {
        $client = ApiClient::query()
            ->with('tokens')
            ->find($this->argument('id'));

        if (! $client) {
            $this->error('API client not found.');

            return self::FAILURE;
        }

        if ($this->option('all')) {
            $count = $client->tokens()->count();

            if ($count === 0) {
                $this->warn(
                    "API client '{$client->name}' has no tokens."
                );

                return self::SUCCESS;
            }

            if (! $this->confirm(
                "Revoke all {$count} token(s) for '{$client->name}'?"
            )) {
                $this->warn('Cancelled.');

                return self::SUCCESS;
            }

            $client->tokens()->delete();

            $this->info(
                "All tokens revoked for '{$client->name}'."
            );

            return self::SUCCESS;
        }

        $tokenId = $this->argument('tokenId');

        if (! $tokenId) {
            if ($client->tokens->isEmpty()) {
                $this->warn(
                    "API client '{$client->name}' has no tokens."
                );

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

            $tokenId = $this->ask('Token ID to revoke');
        }

        $token = $client->tokens()
            ->whereKey($tokenId)
            ->first();

        if (! $token) {
            $this->error(
                "Token ID {$tokenId} not found for '{$client->name}'."
            );

            return self::FAILURE;
        }

        $tokenName = $token->name;

        $token->delete();

        $this->info(
            "Token '{$tokenName}' revoked for '{$client->name}'."
        );

        return self::SUCCESS;
    }
}
