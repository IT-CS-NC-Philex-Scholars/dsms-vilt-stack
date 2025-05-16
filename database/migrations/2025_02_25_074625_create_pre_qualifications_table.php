<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_qualifications', function (Blueprint $table): void {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('contact_number');
            $table->text('address');
            $table->date('birth_date');
            $table->string('gender');
            $table->decimal('current_grade', 5, 2);
            $table->text('enrollment_intent');
            $table->boolean('is_eligible')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_qualifications');
    }
};
