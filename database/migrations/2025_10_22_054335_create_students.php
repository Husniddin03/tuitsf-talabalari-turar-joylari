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
            $table->bigInteger('talaba_id')->unique();
            $table->string('fish')->nullable();
            $table->string('fakultet')->nullable();
            $table->string('guruh')->nullable();
            $table->string('telefon')->nullable();
            $table->string('tyutori')->nullable();
            $table->string('hudud')->nullable();
            $table->string('doimiy_yashash_viloyati')->nullable();
            $table->string('doimiy_yashash_tumani')->nullable();
            $table->string('doimiy_yashash_manzili')->nullable();
            $table->string('doimiy_yashash_manzili_urli')->nullable();
            $table->string('vaqtincha_yashash_viloyati')->nullable();
            $table->string('vaqtincha_yashash_tumani')->nullable();
            $table->string('vaqtincha_yashash_manzili')->nullable();
            $table->string('vaqtincha_yashash_manzili_urli')->nullable();
            $table->string('uy_egasi')->nullable();
            $table->string('uy_egasi_telefoni')->nullable();
            $table->string('yotoqxona_nomeri')->nullable();
            $table->integer('narx')->nullable();
            $table->string('ota_ona')->nullable();
            $table->string('ota_ona_telefoni')->nullable();
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
