<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileResizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_resizes', function (Blueprint $table) {
            $table->increments('id', 11);
            $table->integer('file_id')->unsigned()->index()->nullable();
            $table->string('name', 120)->default('');
            $table->string('url', 520)->default('');
            $table->boolean('is_return_full_url')->default(0);
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
        Schema::dropIfExists('file_resizes');
    }
}
