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
        Schema::create('youtube_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('type')->comment('1:claim,2:content,3:artist');
            $table->string('claim_url')->nullable();
            $table->string('claim_upc')->nullable();
            $table->string('content_upc')->nullable();
            $table->string('artist_channel_link')->nullable();
            $table->string('artist_topic_link')->nullable();
            $table->string('artist_upc1')->nullable();
            $table->string('artist_upc2')->nullable();
            $table->string('artist_upc3')->nullable();
            $table->integer('status')->default(1)->comment('1:pending,2:approved,3:rejected');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('youtube_requests');
    }
};
