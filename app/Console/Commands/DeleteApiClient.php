<?php

namespace App\Console\Commands;

use App\Models\Api\ApiClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteApiClient extends Command
{
    protected $signature = 'api:client:delete
                            {id : API client ID}
                            {--force : Delete without confirmation}';

    protected $description = 'Delete API client and all access tokens';

    public function handle(): int
    {
        $client = ApiClient::query()
            ->withCount('tokens')
            ->find($this->argument('id'));

        if (! $client) {
            $this->error('API client not found.');

            return self::FAILURE;
        }

        $this->table(
            [
                'ID',
                'Name',
                'Status',
                'Tokens',
                'Description',
            ],
            [[
                $client->id,
                $client->name,
                $client->is_active ? 'ACTIVE' : 'DISABLED',
                $client->tokens_count,
                $client->description ?? '-',
            ]]
        );

        if (
            ! $this->option('force')
            && ! $this->confirm(
                "Delete API client '{$client->name}'?"
            )
        ) {
            $this->warn('Cancelled.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($client) {
            $client->tokens()->delete();
            $client->delete();
        });

        $this->info(
            "API client '{$client->name}' deleted."
        );

        return self::SUCCESS;
    }
}
