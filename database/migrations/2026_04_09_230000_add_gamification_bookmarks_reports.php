<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Gamificación (Puntos en usuarios)
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('points')->default(0)->after('bio');
        });

        // 2. Guardados / Bookmarks
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'post_id']);
        });

        // 3. Sistema de Reportes (Polimórfico para reportar posts, usuarios o comentarios)
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->morphs('reportable');
            $table->string('reason');
            $table->enum('status', ['pending', 'reviewed', 'resolved'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('bookmarks');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('points');
        });
    }
};
