<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCandidatesRecordTable extends Migration
{
    /**
     * Run the migrations.
     * 候选人票数
     * 
     * @return void
     */
    public function up()
    {
        Schema::create('candidates_record', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('candidate_id')->comment('候选人id');
            $table->integer('ballot')->unsigned()->default(0)->comment('所得票数');
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
        Schema::dropIfExists('candidates_record');
    }
}
