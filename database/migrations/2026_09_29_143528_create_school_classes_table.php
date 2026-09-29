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
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->string('internal_id')->nullable()->unique();
            $table->unsignedTinyInteger('year'); // 1..5
            $table->string('section', 2); // a letter, allow 2 for flexibility
            $table->string('track')->nullable();
            $table->timestamps();
            $table->unique(['year', 'section']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};
