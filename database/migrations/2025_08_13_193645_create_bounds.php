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
        Schema::create('bounds', function (Blueprint $table) {
            $table->id();
            $table->string('min_lat');
            $table->string('max_lat');
            $table->string('min_lon');
            $table->string('max_lon');
            $table->string('temp_min')->nullable();
            $table->string('temp_max')->nullable();
            $table->string('salt_min')->nullable();
            $table->string('salt_max')->nullable();
            $table->string('w_min')->nullable();
            $table->string('w_max')->nullable();
            $table->string('rain_min')->nullable();
            $table->string('rain_max')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bounds');
    }
};
