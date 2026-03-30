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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // For VIP plans
            $table->foreignId('coach_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('vip_request_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('type', ['diet', 'workout']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content'); // JSON or text content
            $table->integer('daily_calories')->nullable(); // For diet plans
            $table->boolean('is_free')->default(false); // Free plans created by admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};


