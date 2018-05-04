<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParticipantInfoTable extends Migration
{
    /**
     * Run the migrations.
     * 参与者信息
     * 
     * @return void
     */
    public function up()
    {
        Schema::create('participant_info', function (Blueprint $table) {
            $table->increments('id');
            $table->char('uniquekey', 36)->unique()->comment('参与者key');
            $table->string('name')->comment('参与者昵称');
            $table->enum('plat_from', [1, 2, 3, 4])->comment('参与者来源：1-QQ，2-weibo，3-Wechat，4-Other');
            $table->text('detail')->comment('参与者详细信息');
            $table->timestamp('create_at')->useCurrent()->comment('参与者创建时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('participant_info');
    }
}
