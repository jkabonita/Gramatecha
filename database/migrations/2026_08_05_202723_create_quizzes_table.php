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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->integer('time_limit')->nullable()->comment('Time limit in minutes');
            $table->integer('passing_score')->default(60)->comment('Passing score percentage');
            $table->boolean('is_active')->default(true);
            $table->integer('attempts_allowed')->default(0)->comment('0 = unlimited');
            $table->boolean('show_correct_answers')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
