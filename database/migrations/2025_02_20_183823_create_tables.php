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
        Schema::create('scholars', function (Blueprint $table) {
                $table->id();
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
            Schema::create('scholarships', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->text('description');
                    $table->decimal('amount', 10, 2);
                    $table->json('requirements');
                    $table->date('application_deadline');
                    $table->enum('status', ['active', 'inactive']);
                    $table->timestamps();
                    $table->softDeletes();
                });
                Schema::create('requirements', function (Blueprint $table) {
                        $table->id();
                        $table->foreignId('scholar_id')->constrained()->onDelete('cascade');
                        $table->foreignId('scholarship_id')->constrained()->onDelete('cascade');
                        $table->string('document_type');
                        $table->string('file_path');
                        $table->enum('status', ['pending', 'approved', 'rejected']);
                        $table->text('remarks')->nullable();
                        $table->timestamp('submitted_at');
                        $table->timestamp('reviewed_at')->nullable();
                        $table->timestamps();
                        $table->softDeletes();
                    });
                    Schema::create('announcements', function (Blueprint $table) {
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
