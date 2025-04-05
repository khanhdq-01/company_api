<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('member_others', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên thành viên
            $table->string('position')->nullable(); // Cho phép null
            $table->string('image_path')->nullable(); // Cho phép null
            $table->timestamps(); // Tạo cột created_at và updated_at
        });
    }
};
