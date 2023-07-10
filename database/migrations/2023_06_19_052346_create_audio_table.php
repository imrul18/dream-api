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
        Schema::create('audio', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('writter')->nullable();
            $table->date('main_release_date')->nullable();
            $table->date('original_release_date')->nullable();
            $table->string('language_id')->nullable();
            $table->string('genre_id')->nullable();
            $table->string('subgenre_id')->nullable();
            $table->string('label_id')->nullable();
            $table->string('format_id')->nullable();
            $table->string('p_line')->nullable();
            $table->string('c_line')->nullable();
            $table->string('upc')->nullable();
            $table->string('isrc')->nullable();
            $table->string('parental_advisory_id')->nullable();
            $table->string('producer_catalogue_number')->nullable();
            $table->boolean('is_caller_tune')->default(false);
            $table->enum('status', [1, 2, 3, 4])->default(1);
            $table->text('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audio');
    }
};
