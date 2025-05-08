<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('escort_models', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('name', 100);
            $table->integer('age');
            $table->enum('gender', ['female', 'male', 'transgender']);
            $table->string('ethnicity', 50)->nullable();
            $table->integer('height_cm')->nullable();
            $table->integer('weight_kg')->nullable();
            $table->string('bust_size', 10)->nullable();
            $table->string('hair_color', 50)->nullable();
            $table->string('eye_color', 50)->nullable();
            $table->text('language_spoken')->nullable();

            // Contact & Location
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('email', 100)->nullable();

            // Services & Pricing
            $table->text('services_offered')->nullable();
            $table->decimal('price_hourly', 10, 2)->nullable();
            $table->decimal('price_overnight', 10, 2)->nullable();
            $table->boolean('available')->default(true);

            // Images
            $table->string('main_image')->nullable();
            $table->text('gallery_images')->nullable(); // JSON or comma-separated

            // Profile
            $table->text('description')->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('reviews_count')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escort_models');
    }
};
