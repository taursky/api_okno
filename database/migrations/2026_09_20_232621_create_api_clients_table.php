<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_clients', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->string('description')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_clients');
    }
};
