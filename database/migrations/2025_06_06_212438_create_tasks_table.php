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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(1);
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('list_task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('category', ['marketing', 'développement', 'communication'])->nullable();
            $table->enum('priority', ['basse', 'moyenne', 'élevée'])->default('basse');
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index(['project_id']);
            $table->index(['list_task_id', 'order']);
            $table->index('user_id');
            $table->index('category');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
