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
        if (Schema::hasTable('weather_data')) {
            return;
        }

        Schema::create('weather_data', function (Blueprint $table) {
            $table->id();
            $table->decimal('temperature', 4, 2);
            $table->unsignedInteger('humidity');
            $table->unsignedInteger('pressure');
            $table->decimal('wind_speed', 4, 2);
            $table->string('description', 255);
            $table->string('icon', 10);
            $table->timestamp('created_at')->useCurrent();
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_data');
    }
};
