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
        Schema::create('document_files', function (Blueprint $table) {
            $table->id();
            $table->uuid('process_id');
            $table->string('file_name');
            $table->string('status');
            $table->bigInteger('word_count')->default(0);
            $table->bigInteger('line_count')->default(0);
            $table->bigInteger('character_count')->default(0);
            $table->json('frequent_words')->nullable();
            $table->timestamps();
            $table->foreign('process_id')->references('id')->on('processes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_files');
    }
};
