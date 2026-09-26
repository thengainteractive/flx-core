<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('venue_locations')->onDelete('restrict');
            $table->foreignId('venue_type_id')->constrained('venue_types')->onDelete('restrict');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->string('city_area')->nullable();
            
            $table->string('pricing_unit')->default('per_day');
            $table->integer('price')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            
            $table->integer('max_floating_capacity')->nullable();
            $table->integer('max_seated_capacity')->nullable();
            $table->integer('rooms_available')->default(0);
            $table->integer('total_area_sq_ft')->nullable();
            $table->integer('parking_spots')->default(0);
            
            $table->string('catering_policy')->nullable();
            $table->string('alcohol_policy')->nullable();
            
            $table->json('highlights')->nullable();
            $table->json('amenities')->nullable();
            $table->json('rules')->nullable();
            
            $table->boolean('verified')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
