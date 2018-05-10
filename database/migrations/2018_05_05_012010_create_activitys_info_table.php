<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitysInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activitys_info', function (Blueprint $table) {
            $table->increments('id');
            $table->char('uniquekey', 36)->unique()->comment('活动key');
            $table->string('name')->comment('活动名称');
            $table->string('intro', 512)->comment('活动介绍');
            $table->string('host')->nullable()->comment('主办方');
            $table->string('undertake')->nullable()->comment('承办方');
            $table->string('sponsored')->nullable()->comment('赞助方');
            $table->integer('refresh_period')->default(1)->comment('票数刷新周期，单位：天');
            $table->integer('refresh_ballot')->default(1)->comment('每周期可投票数');
            $table->enum('user_from', [1, 2, 3, 4])->comment('投票者来源：1-QQ，2-Weibo，3-Wechat，4-Other');
            $table->json('rules')->nullable()->comment('活动规则，字符串数组，可选');
            $table->json('ava_type')->comment('开放的参赛作品类型，布尔值，1:图片，2：视频，3：音频，4：外链');
            $table->string('creator')->comment('活动创建者');
            $table->string('backimg', 512)->default('https://google.com')->comment('投票页面的背景图');
            $table->string('logo', 512)->default('https://google.com')->comment('投票页面的logo');
            $table->timestamp('start_at')->useCurrent()->comment('开始时间');
            $table->timestamp('end_at')->useCurrent()->comment('结束时间');
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
        Schema::dropIfExists('activitys_info');
    }
}
