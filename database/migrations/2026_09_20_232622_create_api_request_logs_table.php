<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('api_request_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_client_id')
                ->nullable()
                ->constrained('api_clients')
                ->nullOnDelete();
            $table->string('method', 10);
            $table->string('endpoint', 500);
            $table->string('route_name')->nullable();
            $table->string('request_ip', 45)->nullable();
            $table->string('user_agent', 1000)->nullable();
            $table->json('request_data')->nullable();
            $table->unsignedSmallInteger('response_code');
            $table->json('response_data')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('execution_time_ms')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['api_client_id', 'created_at']);
            $table->index(['response_code', 'created_at']);
            $table->index('endpoint');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_request_logs');
    }
};
