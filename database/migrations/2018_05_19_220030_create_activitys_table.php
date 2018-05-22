<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activitys', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('活动名称');
            $table->string('intro', 512)->comment('活动介绍');
            $table->string('host')->nullable()->comment('主办方');
            $table->string('undertake')->nullable()->comment('承办方');
            $table->string('sponsored')->nullable()->comment('赞助方');
            $table->integer('refresh_period')->default(1)->comment('票数刷新周期，单位：天');
            $table->integer('refresh_chance')->default(1)->comment('每周期可投票数');
            $table->enum('user_from', ['QQ', 'Weibo', 'Wechat', 'Other'])->comment('投票者来源');
            $table->text('rules')->nullable()->comment('活动规则，字符串数组，可选');
            $table->enum('type', ["img", "video", "audio", "link"])->comment('比赛类型');
            $table->string('backimg', 512)->default('default/default-bg.jpg')->comment('投票页面的背景图');
            $table->string('logo', 512)->default('default/default-logo.jpg')->comment('投票页面的logo');
            $table->string('creator')->nullable()->default('未知')->comment('创建者');
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
        Schema::dropIfExists('activitys');
    }
}
