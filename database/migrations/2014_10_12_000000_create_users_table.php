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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('status', 20)->default('active')->index();
            $table->string('nickname', 50);
            $table->string('name', 50);
            $table->string('email', 254)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 128);
            $table->string('profile', 160)->nullable();
            $table->string('user_image', 100)->nullable()->default(NULL);
            $table->string('header_image', 100)->nullable()->default(NULL);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
