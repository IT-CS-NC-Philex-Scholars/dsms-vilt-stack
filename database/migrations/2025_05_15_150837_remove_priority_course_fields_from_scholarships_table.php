<?php

declare(strict_types=1);

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
        Schema::table('scholarships', function (Blueprint $table) {
            if (Schema::hasColumn('scholarships', 'is_for_priority_courses')) {
                $table->dropColumn('is_for_priority_courses');
            }
            if (Schema::hasColumn('scholarships', 'priority_courses_description')) {
                $table->dropColumn('priority_courses_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            // Add columns back if they don't exist, making reasonable assumptions about their previous state
            if (!Schema::hasColumn('scholarships', 'is_for_priority_courses')) {
                $table->boolean('is_for_priority_courses')->default(false)->after('target_student_group'); // Adjust 'after' if needed
            }
            if (!Schema::hasColumn('scholarships', 'priority_courses_description')) {
                $table->text('priority_courses_description')->nullable()->after('is_for_priority_courses');
            }
        });
    }
};
