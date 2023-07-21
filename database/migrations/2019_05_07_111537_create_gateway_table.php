<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGatewayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gateway', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('网关名称');
            $table->string('realm')->comment('网关IP，如果端口不是5060，默认格式为：xxx.xxx.xxx.xxx:port');
            $table->string('username')->comment('帐号');
            $table->string('password')->comment('密码');
            $table->string('prefix')->nullable()->comment('前缀');
            $table->string('outbound_caller_id')->nullable()->comment('出局号码');
            $table->decimal('rate',10,2)->default(0)->comment('费率：每分钟多少元');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `gateway` comment '中继网关表'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gateway');
    }
}
