<?php

use App\Enums\ClassroomBuilding;
use App\Enums\ClassroomType;
use App\Enums\ClassroomFloor;
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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('internal_id', 10)->unique()->nullable();
            $table->string('description');
            $table->unsignedInteger('capacity')->default(20);
            $table->string('floor')->default(ClassroomFloor::TERRA->value);
            $table->string('building')->default(ClassroomBuilding::SEDE_CENTRALE->value);
            $table->string('type')->default(ClassroomType::AULA->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
