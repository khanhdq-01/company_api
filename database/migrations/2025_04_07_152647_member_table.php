<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên thành viên
            $table->string('position'); // Chức vụ
            $table->text('description'); // Mô tả về thành viên
            $table->string('image_path'); // Đường dẫn ảnh lưu trong storage
            $table->timestamps(); // Tạo cột created_at và updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
