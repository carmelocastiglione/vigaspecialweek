<?php

use App\Enums\ActivityType;
use App\Enums\ClassroomType;
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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('internal_id')->nullable()->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('secondary_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('activity_type')->default(ActivityType::ENRICHMENT->value);
            $table->string('classroom_type')->default(ClassroomType::AULA->value);
            $table->unsignedInteger('max_students')->default(20);
            $table->unsignedTinyInteger('duration')->default(2);
            $table->unsignedTinyInteger('repetitions')->nullable();
            $table->string('track')->nullable();
            $table->string('track_group')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('remedial_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->unique()->constrained('activities')->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('year')->nullable();
            $table->timestamps();
        });

        Schema::create('enrichment_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->unique()->constrained('activities')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('external')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrichment_activities');
        Schema::dropIfExists('remedial_activities');
        Schema::dropIfExists('activities');
    }
};
