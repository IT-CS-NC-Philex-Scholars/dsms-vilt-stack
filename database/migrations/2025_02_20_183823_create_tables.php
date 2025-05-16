<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scholars', function (Blueprint $table): void {
            $table->id();
            $table->integer('user_id');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('contact_number');
            $table->string('address');
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female', 'other']);
            // $table->('school_id');
            $table->string('type');
            $table->string('course')->nullable();
            $table->integer('year_level');
            $table->enum('status', ['active', 'inactive', 'graduated', 'terminated']);
            $table->json('additional_details');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('scholarships', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('amount', 10, 2);
            $table->json('requirements');
            $table->date('application_deadline');
            $table->enum('status', ['active', 'inactive', 'closed']);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('requirements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('scholar_id')->constrained()->onDelete('cascade');
            $table->foreignId('scholarship_id')->constrained()->onDelete('cascade');
            $table->string('document_type');
            $table->string('file_path')->nullable(); // Make it nullable
            $table->enum('status', ['pending', 'approved', 'rejected']);
            $table->text('remarks')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('announcements', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('priority', ['low', 'medium', 'high']);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
