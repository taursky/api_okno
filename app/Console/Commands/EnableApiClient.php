<?php

namespace App\Console\Commands;

use App\Models\Api\ApiClient;
use Illuminate\Console\Command;

class EnableApiClient extends Command
{
    protected $signature = 'api:client:enable {id}';

    protected $description = 'Enable API client';

    public function handle(): int
    {
        $client = ApiClient::query()->find($this->argument('id'));

        if (! $client) {
            $this->error('API client not found.');

            return self::FAILURE;
        }

        if ($client->is_active) {
            $this->warn("API client '{$client->name}' is already enabled.");

            return self::SUCCESS;
        }

        $client->update([
            'is_active' => true,
        ]);

        $this->info("API client '{$client->name}' enabled.");

        return self::SUCCESS;
    }
}
