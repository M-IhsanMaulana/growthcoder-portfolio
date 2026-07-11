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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            // Profile & Identity
            $table->string('owner_full_name');
            $table->string('owner_title')->nullable();
            $table->foreignId('profile_photo_id')->nullable()->constrained('media')->nullOnDelete();

            // Hero Section
            $table->string('hero_headline');
            $table->text('hero_subheadline')->nullable();
            $table->string('hero_cta_text')->nullable();
            $table->string('hero_cta_url')->nullable();

            // CV File
            $table->string('cv_file_path')->nullable();

            // Social Media links
            $table->string('social_linkedin')->nullable();
            $table->string('social_github')->nullable();
            $table->string('social_telegram')->nullable();
            $table->string('social_instagram')->nullable();
            $table->string('social_twitter')->nullable();
            $table->string('contact_email')->nullable();

            // SEO Metadata Fallback
            $table->string('site_name')->default('growthcoder.id');
            $table->string('meta_title_suffix')->nullable();
            $table->text('default_meta_desc')->nullable();
            $table->foreignId('default_og_image_id')->nullable()->constrained('media')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
