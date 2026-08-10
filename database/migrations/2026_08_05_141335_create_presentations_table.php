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
        Schema::create('presentations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('filename');
            $table->unsignedBigInteger('file_size');
            $table->string('file_type', 10); // pptx, odp, pdf, etc.
            $table->string('file_path'); // storage path
            $table->text('file_url'); // public URL
            $table->unsignedInteger('views_count')->default(0);
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->timestamps();

            // Add indexes
            $table->index('uploaded_by', 'idx_uploaded_by');
            $table->index('category_id', 'idx_category_id');
            $table->index('created_at', 'idx_created_at');
            $table->index('file_type', 'idx_file_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presentations');
    }
};
