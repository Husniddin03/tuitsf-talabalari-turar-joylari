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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('fish');
            $table->string('fakultet');
            $table->string('guruh');
            $table->string('telefon')->nullable();
            $table->string('tyutori')->nullable();
            $table->string('hudud')->nullable();
            $table->string('manzil')->nullable();
            $table->string('uy_egasi')->nullable();
            $table->string('uy_egasi_telefoni')->nullable();
            $table->string('narx')->nullable();
            $table->string('ota_ona')->nullable();
            $table->string('ota_ona_telefoni')->nullable();
            $table->string('url_manzil')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
