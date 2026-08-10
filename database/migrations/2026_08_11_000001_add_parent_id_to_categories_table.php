<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Self-referencing FK: null = top-level parent, non-null = sub-category
            $table->foreignId('parent_id')
                ->nullable()
                ->after('id')
                ->constrained('categories')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeignIdFor('categories', 'parent_id');
            $table->dropColumn('parent_id');
        });
    }
};
