<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            $table->string('category', 20)->default('main')->after('description');
            $table->string('difficulty', 10)->default('easy')->after('category');
            $table->jsonb('ingredients')->default('[]')->after('minutes');
            $table->jsonb('steps')->default('[]')->after('ingredients');
            $table->string('image_path')->nullable()->after('steps');

            $table->index('category');
        });

        DB::statement("ALTER TABLE recipes ADD CONSTRAINT recipes_category_valid CHECK (category IN ('breakfast','main','soup','snack','dessert','drink'))");
        DB::statement("ALTER TABLE recipes ADD CONSTRAINT recipes_difficulty_valid CHECK (difficulty IN ('easy','medium','hard'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE recipes DROP CONSTRAINT IF EXISTS recipes_category_valid');
        DB::statement('ALTER TABLE recipes DROP CONSTRAINT IF EXISTS recipes_difficulty_valid');

        Schema::table('recipes', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropColumn(['description', 'category', 'difficulty', 'ingredients', 'steps', 'image_path']);
        });
    }
};