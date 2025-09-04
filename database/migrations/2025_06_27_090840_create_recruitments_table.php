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
        Schema::create('recruitments', function (Blueprint $table) {
            $table->id();
            $table->string('position'); // Vị trí tuyển dụng
            $table->string('department'); // Phòng ban
            $table->integer('quantity'); // Số lượng
            $table->enum('job_type', ['full-time', 'part-time', 'internship']);
            $table->date('start_date'); // Ngày bắt đầu tuyển
            $table->date('deadline');   // Hạn nộp CV
            $table->enum('status', ['open', 'closed', 'pending']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruitments');
    }
};
