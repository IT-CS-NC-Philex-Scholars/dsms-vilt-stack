<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up()
         {
             Schema::table('requirements', function (Blueprint $table) {
                 $table->string('file_path')->nullable()->change();
                 $table->timestamp('submitted_at')->nullable()->change();
                 $table->timestamp('reviewed_at')->nullable()->change();
                 $table->text('remarks')->nullable()->change();
             });
         }

         public function down()
         {
             Schema::table('requirements', function (Blueprint $table) {
                 $table->string('file_path')->nullable(false)->change();
                 $table->timestamp('submitted_at')->nullable(false)->change();
                 $table->timestamp('reviewed_at')->nullable(false)->change();
                 $table->text('remarks')->nullable(false)->change();
             });
         }
};
