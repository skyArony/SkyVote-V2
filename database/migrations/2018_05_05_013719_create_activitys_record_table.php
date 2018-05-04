<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitysRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activitys_record', function (Blueprint $table) {
            $table->increments('id');
            $table->char('activity_key', 36)->comment('活动key');
            $table->integer('pv')->default(0)->comment('浏览量');
            $table->integer('uv')->default(0)->comment('浏览人数');
            $table->foreign('activity_key')->references('uniquekey')->on('activitys_info')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activitys_record');
    }
}
