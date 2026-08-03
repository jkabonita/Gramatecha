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
            $table->string('cloudinary_public_id')->unique();
            $table->text('cloudinary_url');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Add indexes
            $table->index('uploaded_by', 'idx_uploaded_by');
            $table->index('created_at', 'idx_created_at');
            $table->index('cloudinary_public_id', 'idx_cloudinary_public_id');
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
