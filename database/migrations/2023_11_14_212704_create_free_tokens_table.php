<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('free_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('organisation_name');
            $table->string('organisation_email');
            $table->string('freeToken');
            $table->timestamp('token_expires_at');
            $table->timestamps();
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('free_tokens');
    }
};
