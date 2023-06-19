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
        Schema::create('audio_arrangers', function (Blueprint $table) {
            $table->id();
            $table->integer('audio_id');
            $table->string('arranger');
            $table->boolean('isPrimary');
            $table->integer('sequence_number');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audio_arrangers');
    }
};
