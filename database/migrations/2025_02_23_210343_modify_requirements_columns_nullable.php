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
        Schema::table('requirements', function (Blueprint $table): void {
            $table->string('file_path')->nullable()->change();
            $table->timestamp('submitted_at')->nullable()->change();
            $table->timestamp('reviewed_at')->nullable()->change();
            $table->text('remarks')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('requirements', function (Blueprint $table): void {
            $table->string('file_path')->nullable(false)->change();
            $table->timestamp('submitted_at')->nullable(false)->change();
            $table->timestamp('reviewed_at')->nullable(false)->change();
            $table->text('remarks')->nullable(false)->change();
        });
    }
};
