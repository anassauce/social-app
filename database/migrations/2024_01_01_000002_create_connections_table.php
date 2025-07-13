<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('connected_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'blocked'])->default('accepted');
            $table->timestamp('connected_at');
            $table->timestamps();

            $table->unique(['user_id', 'connected_user_id']);
            $table->index(['user_id', 'status']);
            $table->index(['connected_user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connections');
    }
};