<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyInforsTable extends Migration
{
    public function up()
    {
        Schema::create('company_infors', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->text('address');
            $table->string('director_name');
            $table->date('founded_date')->nullable();
            $table->text('business_scope')->nullable();
            $table->string('capital')->nullable(); // dùng string nếu không cần tính toán
            $table->string('group_parent')->nullable();
            $table->json('group_subsidiaries')->nullable(); // lưu array JSON
            $table->unsignedInteger('employee_count')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_infors');
    }
}
