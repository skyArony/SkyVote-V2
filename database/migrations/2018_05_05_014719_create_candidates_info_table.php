<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCandidatesInfoTable extends Migration
{
    /**
     * Run the migrations.
     * 候选人信息
     * 
     * @return void
     */
    public function up()
    {
        Schema::create('candidates_info', function (Blueprint $table) {
            $table->increments('id');
            $table->char('uniquekey', 36)->unique()->comment('候选人key');
            $table->string('name')->comment('昵称');
            $table->string('tel')->nullable()->comment('手机号');
            $table->string('QQ')->nullable()->comment('QQ');
            $table->string('intro', 512)->comment('介绍');
            $table->char('belong_ac', 36)->comment('所属活动key');
            $table->enum('type', [1, 2, 3, 4])->comment('参赛作品类型：1-图片，2-视频，3-音频，4-外链');
            $table->string('img_url', 512)->nullable()->comment('图片链接');
            $table->string('video_url', 512)->nullable()->comment('视频链接');
            $table->string('audio_url', 512)->nullable()->comment('音频链接');
            $table->string('link_url', 512)->nullable()->comment('外链链接');
            $table->string('linkcover_url', 512)->nullable()->comment('外链封面链接');
            $table->timestamps();
            $table->foreign('belong_ac')->references('uniquekey')->on('activitys_info')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidates_info');
    }
}
