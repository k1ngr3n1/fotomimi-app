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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('filename');
            $table->string('filepath');
            $table->enum('category', ['wedding', 'baptism', 'concert', 'studio', 'modelling', 'other']);
            $table->enum('type', ['photo', 'video']);
            $table->bigInteger('file_size')->nullable(); // in bytes
            $table->string('dimensions')->nullable(); // e.g., "1920x1080"
            $table->string('alt_text')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['category', 'type']);
            $table->index('is_featured');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
