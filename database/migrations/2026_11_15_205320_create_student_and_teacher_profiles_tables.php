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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained()->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->restrictOnDelete();
            $table->foreignId('coordinator_class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->foreignId('responsible_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('internal_id')->nullable()->unique();
            $table->string('job')->nullable();
            $table->string('contract')->nullable();
            $table->boolean('no_first_hour')->default(false);
            $table->integer('hours')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_profiles');
        Schema::dropIfExists('student_profiles');
    }
};
