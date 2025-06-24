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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
                $table->string('code')->unique(); // e.g., CSC101, MTH203
                $table->text('description')->nullable();
                $table->integer('max_students')->default(3); // Business rule: max 3 students per course
                $table->foreignId('professor_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('major_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};