<?php

namespace App\Console\Commands;

use App\Models\Api\ApiClient;
use Illuminate\Console\Command;

class CreateApiClient extends Command
{
    protected $signature = 'api:client:create
                            {name? : Client name}
                            {--description= : Client description}
                            {--token-name=default : Token name}
                            {--expires= : Expiration in days}
                            {--abilities=* : Token abilities}';

    protected $description = 'Create API client and access token';

    public function handle(): int
    {
        $name = $this->argument('name')
            ?: $this->ask('Client name');

        if (! $name) {
            $this->error('Client name is required.');

            return self::FAILURE;
        }

        $description = $this->option('description');

        if ($description === null) {
            $description = $this->ask(
                'Description (optional)',
                ''
            );
        }

        $client = ApiClient::query()->create([
            'name' => $name,
            'description' => $description ?: null,
            'is_active' => true,
        ]);

        $expiresAt = null;

        if ($days = $this->option('expires')) {
            $days = (int) $days;

            if ($days <= 0) {
                $client->delete();

                $this->error('--expires must be greater than 0.');

                return self::FAILURE;
            }

            $expiresAt = now()->addDays($days);
        }

        $abilities = $this->parseAbilities(
            $this->option('abilities')
        );

        $token = $client->createToken(
            $this->option('token-name'),
            $abilities,
            $expiresAt,
        );

        $this->newLine();

        $this->info('API client successfully created.');

        $this->table(
            ['ID', 'Name', 'Description', 'Expires'],
            [[
                $client->id,
                $client->name,
                $client->description ?: '-',
                $expiresAt?->toDateTimeString() ?? 'Never',
            ]]
        );

        $this->newLine();

        $this->warn('Save this token now. It will not be shown again.');

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
