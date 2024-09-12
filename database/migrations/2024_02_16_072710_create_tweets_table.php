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
        Schema::create('tweets', function (Blueprint $table) {
            $table->id();
            $table->string('status', 20)->default('active');
            $table->integer('user_id')->index();
            $table->string('body', 140)->nullable();
            $table->longText('tweet_image')->nullable();
            $table->integer('reply_id')->nullable();
            $table->integer('retweet_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tweets');
    }
};
