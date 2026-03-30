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
        Schema::create('vip_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('coach_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('age');
            $table->decimal('height', 5, 2); // in cm
            $table->decimal('weight', 5, 2); // in kg
            $table->enum('goal', ['lose', 'gain', 'maintain']);
            $table->enum('activity_level', ['sedentary', 'light', 'moderate', 'active', 'very_active']);
            $table->text('allergies')->nullable();
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vip_requests');
    }
};


