<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weather_data', function (Blueprint $table) {
            $table->id();
            $table->decimal('temperature', 5, 2);
            $table->unsignedTinyInteger('humidity');
            $table->unsignedSmallInteger('pressure');
            $table->decimal('wind_speed', 5, 2);
            $table->string('description');
            $table->string('icon', 10);
            $table->timestamp('observed_at')->useCurrent();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('observed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_data');
    }
};
