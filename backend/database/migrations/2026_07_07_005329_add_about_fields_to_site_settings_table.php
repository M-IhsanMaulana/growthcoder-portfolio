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
        Schema::table('site_settings', function (Blueprint $table) {
            // About / Biografi fields
            $table->longText('about_bio')->nullable()->after('default_og_image_id');
            $table->string('about_location')->nullable()->after('about_bio');

            // Quick Information fields
            $table->json('about_specialities')->nullable()->after('about_location');

            // Stats fields (flexible JSON array)
            $table->json('about_stats')->nullable()->after('about_specialities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['about_bio', 'about_location', 'about_specialities', 'about_stats']);
        });
    }
};
