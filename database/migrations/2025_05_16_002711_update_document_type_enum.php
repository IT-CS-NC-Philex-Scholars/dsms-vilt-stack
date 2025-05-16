<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to recreate the table because SQLite doesn't support altering ENUM columns
        if (DB::connection()->getDriverName() === 'sqlite') {
            // Create new table with updated ENUM values
            Schema::create('documents_new', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('application_id')->nullable()->constrained()->nullOnDelete();
                $table->string('type')->check("type IN ('residency', 'enrollment', 'grades', 'grade_slip', 'id_card', 'enrollment_certificate', 'income_certificate', 'recommendation_letter')");
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

            // Copy existing data
            DB::statement('INSERT INTO documents_new SELECT * FROM documents');
            
            // Drop old table and rename new one
            Schema::dropIfExists('documents');
            Schema::rename('documents_new', 'documents');
        } else {
            // For other databases, we can use ALTER TABLE to modify the ENUM
            DB::statement("ALTER TABLE documents MODIFY COLUMN type ENUM('residency', 'enrollment', 'grades', 'grade_slip', 'id_card', 'enrollment_certificate', 'income_certificate', 'recommendation_letter')");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            // Create new table with original ENUM values
            Schema::create('documents_old', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('application_id')->nullable()->constrained()->nullOnDelete();
                $table->string('type')->check("type IN ('residency', 'enrollment', 'grades')");
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

            // Copy existing data for 'residency', 'enrollment', 'grades' only
            DB::statement("INSERT INTO documents_old SELECT * FROM documents WHERE type IN ('residency', 'enrollment', 'grades')");
            
            // Drop old table and rename new one
            Schema::dropIfExists('documents');
            Schema::rename('documents_old', 'documents');
        } else {
            // For other databases, we can use ALTER TABLE to restore the ENUM
            DB::statement("ALTER TABLE documents MODIFY COLUMN type ENUM('residency', 'enrollment', 'grades')");
        }
    }
};
