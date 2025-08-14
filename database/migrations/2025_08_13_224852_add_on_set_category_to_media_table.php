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
        Schema::table('media', function (Blueprint $table) {
            // First, drop the index
            $table->dropIndex(['category', 'type']);
        });
        
        Schema::table('media', function (Blueprint $table) {
            // Then drop and recreate the category column with the new enum
            $table->dropColumn('category');
        });
        
        Schema::table('media', function (Blueprint $table) {
            $table->enum('category', ['wedding', 'baptism', 'concert', 'studio', 'modelling', 'travel', 'on-set', 'other'])->default('other')->after('filepath');
            $table->index(['category', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            // Drop the index first
            $table->dropIndex(['category', 'type']);
        });
        
        Schema::table('media', function (Blueprint $table) {
            // Remove the on-set category by recreating the enum without it
            $table->dropColumn('category');
        });
        
        Schema::table('media', function (Blueprint $table) {
            $table->enum('category', ['wedding', 'baptism', 'concert', 'studio', 'modelling', 'travel', 'other'])->after('filepath');
            $table->index(['category', 'type']);
        });
    }
};
