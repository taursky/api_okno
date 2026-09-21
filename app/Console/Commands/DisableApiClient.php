<?php

namespace App\Console\Commands;

use App\Models\Api\ApiClient;
use Illuminate\Console\Command;

class DisableApiClient extends Command
{
    protected $signature = 'api:client:disable {id}';

    protected $description = 'Disable API client';

    public function handle(): int
    {
        $client = ApiClient::query()->find($this->argument('id'));

        if (! $client) {
            $this->error('API client not found.');

            return self::FAILURE;
        }

        $client->update([
            'is_active' => false,
        ]);

        $this->info("API client '{$client->name}' disabled.");

        return self::SUCCESS;
    }
}
