<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->unsignedSmallInteger('minutes');
            $table->boolean('is_favourite')->default(false);
            $table->timestamps();

            $table->index('created_at');
        });

        // The database itself refuses nonsense values.
        DB::statement('ALTER TABLE recipes ADD CONSTRAINT recipes_minutes_positive CHECK (minutes > 0)');
        DB::statement('ALTER TABLE recipes ADD CONSTRAINT recipes_title_not_blank CHECK (char_length(trim(title)) >= 3)');
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};