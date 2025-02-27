<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['residency', 'enrollment', 'grades']);
            $table->string('file_path');
             $table->string('status')->default('pending');
             $table->timestamp('verification_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('original_name');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->boolean('verified')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
