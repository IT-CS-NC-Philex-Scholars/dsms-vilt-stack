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
        Schema::table('applications', function (Blueprint $table): void {
            // Add scholarship_id after user_id (or wherever appropriate)
            $table->foreignId('scholarship_id')
                ->after('user_id') // Adjust position as needed
                ->nullable() // Make nullable initially if data exists, or provide default
                ->constrained('scholarships') // Assumes table name is 'scholarships'
                ->onDelete('cascade'); // Or 'set null' depending on desired behavior

            // Optional: Add submitted_at timestamp
            $table->timestamp('submitted_at')->nullable()->after('status');

            // Make existing columns nullable if they weren't and might be empty initially
            // $table->string('rejection_reason')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table): void {
            // Drop foreign key constraint first
            // The default constraint name is applications_scholarship_id_foreign
            // Verify the name in your DB or specify it if different
            $table->dropForeign(['scholarship_id']);
            $table->dropColumn('scholarship_id');
            $table->dropColumn('submitted_at'); // Optional
        });
    }
};
