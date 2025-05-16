<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('application_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['residency', 'enrollment', 'grades']);
            $table->string('file_path');
            $table->string('status')->default('pending');
            $table->timestamp('verification_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('original_name');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->boolean('verified')->default(false);
            $table->string('semester_type')->nullable();
            $table->integer('semester_number')->nullable();
            $table->integer('academic_year')->nullable();
            $table->string('disk')->default('public');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
