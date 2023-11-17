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
        Schema::create('paid_videos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('place_id');
            $table->string('long_version_self_guided');
            $table->string('long_eng_version_360_video');
            $table->string('long_french_version_360_video');
            $table->string('long_kiny_version_360_video');
            $table->timestamps();
            $table->foreign('place_id')->references('id')->on('places')->onDelete('cascade');
        });;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paid_videos');
    }
};
