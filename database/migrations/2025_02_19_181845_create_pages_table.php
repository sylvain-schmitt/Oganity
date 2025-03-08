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
        Schema::create('pages', function (Blueprint $table) {
            // Champs d'identification
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();

            // Contenu et mise en page
            $table->json('content')->nullable();
            $table->json('styles')->nullable();
            $table->string('template')->default('default');

            // SEO basique
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            // Balises de vérification
            $table->string('meta_google_verification')->nullable();
            $table->string('meta_bing_verification')->nullable();
            $table->string('meta_yandex_verification')->nullable();

            // Balises robots
            $table->string('meta_robots')->default('index,follow');

            // Open Graph
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('og_type')->default('website');

            // Statut et configuration
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->boolean('is_home')->default(false);
            $table->integer('sort_order')->default(0);

            // Relations et timestamps
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('pages')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
