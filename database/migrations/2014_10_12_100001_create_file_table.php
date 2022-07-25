<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file', function (Blueprint $table) {
            $table->increments('id', 11);
            $table->string('project', 120)->default('');
            $table->string('file_name', 120)->default('');
            $table->string('file_size', 120)->default('');
            $table->string('url', 520)->default('');
            $table->boolean('is_return_full_url')->default(0);
            $table->string('resize', 500)->default('');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file');
    }
}
