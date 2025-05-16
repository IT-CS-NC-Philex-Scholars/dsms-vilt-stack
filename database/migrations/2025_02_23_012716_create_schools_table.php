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
        Schema::create('schools', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('province');
            $table->string('region')->nullable();
            $table->string('type')->comment('public, private, state university');
            $table->string('level')->comment('elementary, high school, college/university');
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('additional_info')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('scholars', function (Blueprint $table): void {
            $table->integer('school_id')->constrained('schools');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
