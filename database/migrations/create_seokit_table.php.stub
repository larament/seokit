<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('seokit.table_name', 'seokit'), function (Blueprint $table): void {
            $table->id();
            $table->morphs('model');

            // General SEO
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('canonical')->nullable();
            $table->string('robots')->nullable();

            // Social Media Tags
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('twitter_image')->nullable();

            // Structured Data (JSON-LD schema)
            $table->json('structured_data')->nullable(); 

            // Internal Content Strategy
            $table->boolean('is_cornerstone')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('seokit.table_name', 'seokit'));
    }
};
