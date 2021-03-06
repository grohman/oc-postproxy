<?php namespace IDesigning\PostProxy\Updates;

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class CreateChannelRubricPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postproxy_channel_rubric', function (Blueprint $table) {
            $table->integer('channel_id')->unsigned()->index();
            $table->integer('rubric_id')->unsigned()->index();
            $table->timestamps();

            $table->primary(['channel_id', 'rubric_id']);
            $table->foreign('channel_id')->references('id')->on('postproxy_channels')->onDelete('cascade');
            $table->foreign('rubric_id')->references('id')->on('postproxy_rubrics')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('postproxy_channel_rubric');
    }
}
