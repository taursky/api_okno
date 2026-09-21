<?php

namespace App\Console\Commands;

use App\Models\Api\ApiClient;
use Illuminate\Console\Command;

class CreateApiClientToken extends Command
{
    protected $signature = 'api:client:token
                            {id : API client ID}
                            {--name=default : Token name}
                            {--expires= : Expiration in days}
                            {--abilities=* : Comma-separated token abilities}';

    protected $description = 'Create new access token for API client';

    public function handle(): int
    {
        $client = ApiClient::query()->find($this->argument('id'));

        if (! $client) {
            $this->error('API client not found.');

            return self::FAILURE;
        }

        if (! $client->is_active) {
            $this->error(
                "API client '{$client->name}' is disabled."
            );

            return self::FAILURE;
        }

        $expiresAt = null;

        if ($this->option('expires') !== null) {
            $days = (int) $this->option('expires');

            if ($days <= 0) {
                $this->error('--expires must be greater than 0.');

                return self::FAILURE;
            }

            $expiresAt = now()->addDays($days);
        }

        $abilities = $this->parseAbilities(
            $this->option('abilities')
        );

        $token = $client->createToken(
            $this->option('name'),
            $abilities,
            $expiresAt,
        );

        $this->info(
            "Token created for API client '{$client->name}'."
        );

        $this->table(
            [
                'Client',
                'Token name',
                'Abilities',
                'Expires',
            ],
            [[
                $client->name,
                $this->option('name'),
                implode(', ', $abilities),
                $expiresAt?->format('Y-m-d H:i:s') ?? 'Never',
            ]]
        );

        $this->newLine();

        $this->warn(
            'Save this token now. It will not be shown again.'
        );

        $this->newLine();

        $this->line($token->plainTextToken);

        $this->newLine();

        return self::SUCCESS;
    }

    private function parseAbilities(string|array|null $abilities): array
    {
        if ($abilities === null || $abilities === [] || $abilities === '') {
            return ['*'];
        }

        $abilities = is_array($abilities)
            ? $abilities
            : [$abilities];

        $parsed = collect($abilities)
            ->flatMap(fn (string $ability) => explode(',', $ability))
            ->map(fn (string $ability) => trim($ability))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $parsed === [] ? ['*'] : $parsed;
    }
}
