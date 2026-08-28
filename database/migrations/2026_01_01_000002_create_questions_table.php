<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->json('options'); // ex: ["Paris", "Londres", "Berlin", "Madrid"]
            $table->unsignedTinyInteger('correct_option'); // index dans le tableau options (0-3)
            $table->enum('difficulty', ['facile', 'moyen', 'difficile'])->default('moyen');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
