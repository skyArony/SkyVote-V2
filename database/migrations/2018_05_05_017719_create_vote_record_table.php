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
            $table->char('ip', 15)->comment('投票IP');
            $table->string('area')->nullable()->comment('投票地区');
            $table->timestamp('vote_at')->useCurrent()->comment('投票时间');
            $table->string('voter_key')->comment('投票者key');
            $table->unsignedInteger('activity_id')->comment('活动id');
            $table->unsignedInteger('candidate_id')->comment('候选人id');
            $table->foreign('voter_key')->references('uniquekey')->on('voter')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('activities')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('candidate_id')->references('id')->on('candidates')->onUpdate('cascade')->onDelete('cascade');
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
