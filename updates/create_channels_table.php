<?php namespace IDesigning\PostProxy\Updates;

use DB;
use Schema;
use October\Rain\Database\Updates\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChannelsTable extends Migration
{

    public function up()
    {
        Schema::create('postproxy_channels', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->integer('service_id')->unsigned()->nullable();
            $table->text('options');
            $table->string('state')->nullable();
            $table->timestamps();
            $table->foreign('service_id')->references('id')->on('postproxy_services')->onDelete(DB::raw('set null'));
        });
    }

    public function down()
    {
        Schema::dropIfExists('postproxy_channels');
    }

}
