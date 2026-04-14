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
        Schema::create('liked_songs', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained()->cascadeOnDelete();
            $blueprint->string('spotify_id')->index();
            $blueprint->string('name');
            $blueprint->string('artist');
            $blueprint->string('album');
            $blueprint->string('image')->nullable();
            $blueprint->integer('duration_ms');
            $blueprint->string('preview_url')->nullable();
            $blueprint->string('uri');
            $blueprint->dateTime('added_at');
            $blueprint->timestamps();

            $blueprint->unique(['user_id', 'spotify_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liked_songs');
    }
};
