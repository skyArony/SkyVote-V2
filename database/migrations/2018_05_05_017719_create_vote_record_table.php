<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoteRecordTable extends Migration
{
    /**
     * Run the migrations.
     * 每一票的记录
     * 
     * @return void
     */
    public function up()
    {
        Schema::create('vote_record', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('plat_from', [1, 2, 3, 4])->comment('投票来源：1-QQ，2-weibo，3-Wechat，4-Other');
            $table->char('ip', 15)->comment('投票IP');
            $table->string('area')->nullable()->comment('投票地区');
            $table->timestamp('vote_at')->useCurrent()->comment('投票时间');
            $table->string('voter_name')->comment('投票者昵称');
            $table->char('voter_key', 36)->comment('投票者key');
            $table->char('activity_key', 36)->comment('活动key');
            $table->char('candidate_key', 36)->comment('候选人key');
            $table->foreign('voter_key')->references('uniquekey')->on('participant_info')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('activity_key')->references('uniquekey')->on('activitys_info')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('candidate_key')->references('uniquekey')->on('candidates_info')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vote_record');
    }
}
