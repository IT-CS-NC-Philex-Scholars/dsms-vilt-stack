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
            // == Columns to DROP (from previous revamp attempt) ==
            $columnsToDrop = [
                'award_amount', 
                'award_frequency',
                'scholarship_type', // Will be replaced by scholarship_program_type
                'coverage_details',
                // 'eligibility_criteria_description', // Keep this, it's general enough
                // 'required_documents_description', // Will be renamed
                // 'application_instructions', // Will be renamed
                'evaluation_start_date',
                'evaluation_end_date',
                'award_notification_date',
                'funding_source',
                'target_program_level',
                'target_course_study'
            ];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('scholarships', $column)) {
                    $table->dropColumn($column);
                }
            }

            // == Columns to RENAME ==
            if (Schema::hasColumn('scholarships', 'application_start_date')) {
                $table->renameColumn('application_start_date', 'application_period_start');
            }
            // application_end_date was already renamed from application_deadline, let's rename it to application_period_end
            if (Schema::hasColumn('scholarships', 'application_end_date')) {
                $table->renameColumn('application_end_date', 'application_period_end');
            }
            if (Schema::hasColumn('scholarships', 'available_slots')) {
                $table->renameColumn('available_slots', 'slots_available');
            }
             if (Schema::hasColumn('scholarships', 'required_documents_description')) {
                $table->renameColumn('required_documents_description', 'documentary_requirements_description');
            }
            if (Schema::hasColumn('scholarships', 'application_instructions')) {
                $table->renameColumn('application_instructions', 'application_process_description');
            }


            // == Add NEW CHED-inspired columns (or ensure they exist if run multiple times) ==
            if (!Schema::hasColumn('scholarships', 'scholarship_program_type')) {
                $table->string('scholarship_program_type')->nullable()->after('description');
            }
            if (!Schema::hasColumn('scholarships', 'financial_assistance_type')) {
                $table->string('financial_assistance_type')->nullable()->after('scholarship_program_type');
            }
            if (!Schema::hasColumn('scholarships', 'target_student_group')) {
                $table->string('target_student_group')->nullable()->after('financial_assistance_type');
            }
            if (!Schema::hasColumn('scholarships', 'is_for_priority_courses')) {
                $table->boolean('is_for_priority_courses')->default(false)->after('target_student_group');
            }
            if (!Schema::hasColumn('scholarships', 'priority_courses_description')) {
                $table->text('priority_courses_description')->nullable()->after('is_for_priority_courses');
            }
            if (!Schema::hasColumn('scholarships', 'gwa_requirement_description')) {
                $table->text('gwa_requirement_description')->nullable()->after('priority_courses_description');
            }
            if (!Schema::hasColumn('scholarships', 'income_bracket_requirement_description')) {
                $table->text('income_bracket_requirement_description')->nullable()->after('gwa_requirement_description');
            }
            if (!Schema::hasColumn('scholarships', 'benefits_description')) {
                $table->text('benefits_description')->nullable()->after('slots_available'); // Position after slots_available
            }

            // Modify status column for new CHED-inspired options
            $table->string('status')->default('upcoming')
                  ->comment("Status: upcoming, accepting_applications, screening, awarding, ongoing, completed, archived")
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            // == Revert RENAME operations ==
            if (Schema::hasColumn('scholarships', 'application_period_start')) {
                $table->renameColumn('application_period_start', 'application_start_date');
            }
            if (Schema::hasColumn('scholarships', 'application_period_end')) {
                $table->renameColumn('application_period_end', 'application_end_date');
            }
            if (Schema::hasColumn('scholarships', 'slots_available')) {
                $table->renameColumn('slots_available', 'available_slots');
            }
            if (Schema::hasColumn('scholarships', 'documentary_requirements_description')) {
                $table->renameColumn('documentary_requirements_description', 'required_documents_description');
            }
            if (Schema::hasColumn('scholarships', 'application_process_description')) {
                $table->renameColumn('application_process_description', 'application_instructions');
            }

            // == Columns to ADD BACK (that were specific to the first revamp but now dropped) ==
            // For simplicity, just listing them. Their exact previous attributes (nullable, after, comment) are not restored here.
            $columnsToAddBack = [
                'award_amount' => 'decimal', 
                'award_frequency' => 'string',
                'scholarship_type' => 'string',
                'coverage_details' => 'text',
                'evaluation_start_date' => 'date',
                'evaluation_end_date' => 'date',
                'award_notification_date' => 'date',
                'funding_source' => 'string',
                'target_program_level' => 'string',
                'target_course_study' => 'string'
            ];
            foreach ($columnsToAddBack as $column => $type) {
                if (!Schema::hasColumn('scholarships', $column)) {
                    if ($type === 'decimal') {
                        $table->decimal($column, 10, 2)->nullable();
                    } elseif ($type === 'date'){
                        $table->date($column)->nullable();
                    } else {
                        $table->{$type}($column)->nullable();
                    }
                }
            }

            // == Columns to DROP (the new CHED-inspired ones) ==
            $chedColumnsToDrop = [
                'scholarship_program_type',
                'financial_assistance_type',
                'target_student_group',
                'is_for_priority_courses',
                'priority_courses_description',
                'gwa_requirement_description',
                'income_bracket_requirement_description',
                'benefits_description'
            ];
            $table->dropColumn($chedColumnsToDrop);

            // Revert status column to a previous state (example)
            $table->string('status')->default('draft')->comment("Status options: 'draft', 'active', 'evaluating', 'awarding_completed', 'closed', 'archived'")->change();
        });
    }
};
