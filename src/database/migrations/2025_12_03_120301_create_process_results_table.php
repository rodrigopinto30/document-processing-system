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
        Schema::create('process_results', function (Blueprint $table) {
            $table->id();
            $table->uuid('process_id');
            $table->bigInteger('total_words')->default(0);
            $table->bigInteger('total_lines')->default(0);
            $table->bigInteger('total_characters')->default(0);
            $table->json('most_frequent_words')->nullable();
            $table->json('files_processed')->nullable();
            $table->timestamps();

            $table->foreign('process_id')->references('id')->on('processes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_results');
    }
};
