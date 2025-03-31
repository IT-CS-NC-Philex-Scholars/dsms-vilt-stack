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
        Schema::table('requirements', function (Blueprint $table) {
                       // Add the foreign key constraint. Adjust onDelete behavior as needed.
                       $table->foreignId('scholarship_id')->nullable()->constrained()->onDelete('cascade');
                   });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requirements', function (Blueprint $table) {
                    // Drop the foreign key first if it exists
                    $table->dropForeign(['scholarship_id']);
                    $table->dropColumn('scholarship_id');
                });
    }
};
