<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voter', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uniquekey')->unique()->comment('参与者key');
            $table->string('name')->comment('参与者昵称');
            $table->string('avatar')->comment('头像');
            $table->enum('plat_from', ['QQ', 'Weibo', 'Wechat', 'Other'])->comment('参与者来源');
            $table->json('detail')->comment('参与者详细信息');
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
        Schema::dropIfExists('voter');
    }
}
