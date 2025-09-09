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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('service_tag')->nullable();
            $table->string('version')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('status');
            $table->string('device_id')->unique();
            $table->string('client_hostname'); // To identify the client PC
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
