<?php

namespace App\Console\Commands;

use App\Models\Api\ApiClient;
use Illuminate\Console\Command;

class ListApiClients extends Command
{
    protected $signature = 'api:client:list';

    protected $description = 'List API clients';

    public function handle(): int
    {
        $clients = ApiClient::query()
            ->withCount('tokens')
            ->orderBy('id')
            ->get();

        if ($clients->isEmpty()) {
            $this->warn('No API clients found.');

            return self::SUCCESS;
        }

        $this->table(
            [
                'ID',
                'Name',
                'Status',
                'Tokens',
                'Last used',
                'Created',
                'Description',
            ],
            $clients->map(function (ApiClient $client) {
                return [
                    $client->id,
                    $client->name,
                    $client->is_active ? 'ACTIVE' : 'DISABLED',
                    $client->tokens_count,
                    $client->last_used_at?->format('Y-m-d H:i:s') ?? '-',
                    $client->created_at?->format('Y-m-d H:i:s') ?? '-',
                    $client->description ?? '-',
                ];
            })->all()
        );

        return self::SUCCESS;
    }
}
