<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCandidatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('昵称');
            $table->string('tel')->nullable()->comment('手机号');
            $table->string('QQ')->nullable()->comment('QQ');
            $table->string('intro', 512)->comment('介绍');
            $table->unsignedInteger('belong_ac_id')->comment('所属活动id');
            $table->text('textContent')->nullable()->comment('视频、音频、外链类型的容器');
            $table->text('imageContent')->nullable()->comment('外链和和图片类型的容器');
            $table->timestamps();
            $table->foreign('belong_ac_id')->references('id')->on('activitys')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidates');
    }
}
