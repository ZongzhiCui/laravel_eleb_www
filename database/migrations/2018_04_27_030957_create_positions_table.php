<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->defautl('')->comment('姓名');
            $table->string('tel')->defautl('')->comment('电话');
            $table->string('provence')->defautl('')->comment('省份');
            $table->string('city')->defautl('')->comment('城市');
            $table->string('area')->defautl('')->comment('区');
            $table->string('detail_address')->defautl('')->comment('详细地址');
            $table->unsignedInteger('users_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('positions');
    }
}
