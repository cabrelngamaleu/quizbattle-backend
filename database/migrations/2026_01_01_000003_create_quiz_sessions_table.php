<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique(); // code court à partager, ex: "AB3XQ9"
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('questions_count')->default(10);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamp('closes_at')->nullable(); // date limite optionnelle pour jouer
            $table->timestamps();
        });

        // Snapshot des questions exactes tirées pour cette session
        // (pour que le classement reste cohérent même si la banque de questions évolue)
        Schema::create('session_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('order');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_questions');
        Schema::dropIfExists('quiz_sessions');
    }
};
