<?php namespace IDesigning\PostProxy\Updates;

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class CreateChannelRecipientPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postproxy_channel_recipient', function (Blueprint $table) {
            $table->integer('channel_id')->unsigned()->index();
            $table->bigInteger('recipient_id')->unsigned()->index();
            $table->timestamps();

            $table->primary(['channel_id', 'recipient_id']);
            $table->foreign('channel_id')->references('id')->on('postproxy_channels')->onDelete('cascade');
            $table->foreign('recipient_id')->references('id')->on('postproxy_recipients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('postproxy_channel_recipient');
    }
}
