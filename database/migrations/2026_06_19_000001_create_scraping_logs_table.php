<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scraping_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('workflow_name');
            $table->unsignedBigInteger('provinsi_id')->nullable();
            $table->enum('status', ['success', 'failed', 'running']);
            $table->integer('total_pasar')->nullable();
            $table->integer('total_data')->nullable();
            $table->integer('total_insert')->nullable();
            $table->integer('total_skip')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->timestamps();

            $table->foreign('provinsi_id')
                  ->references('id_provinsi')
                  ->on('provinsi')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scraping_logs');
    }
};
