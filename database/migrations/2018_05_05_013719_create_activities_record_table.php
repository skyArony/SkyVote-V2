<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities_record', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('activity_id')->comment('活动id');
            $table->integer('pv')->default(0)->comment('浏览量');
            $table->integer('uv')->default(0)->comment('浏览人数');
            $table->foreign('activity_id')->references('id')->on('activities')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities_record');
    }
}
