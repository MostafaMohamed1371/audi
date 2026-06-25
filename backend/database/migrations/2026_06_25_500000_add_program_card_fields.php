<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->text('card_description_ar')->nullable()->after('hero_intro_en');
            $table->text('card_description_en')->nullable()->after('card_description_ar');
            $table->unsignedInteger('sort_order')->default(0)->after('card_description_en');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['card_description_ar', 'card_description_en', 'sort_order']);
        });
    }
};
