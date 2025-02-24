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
        Schema::create('scholar_scholarship', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('scholar_id')->constrained()->onDelete('cascade');
                    $table->foreignId('scholarship_id')->constrained()->onDelete('cascade');
                    $table->enum('status', ['active', 'inactive', 'completed']);
                    $table->date('start_date');
                    $table->date('end_date')->nullable();
                    $table->text('remarks')->nullable();
                    $table->timestamps();
                    $table->softDeletes();
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholar_scholarship');
    }
};
