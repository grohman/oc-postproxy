<?php namespace IDesigning\PostProxy\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateRubricsTable extends Migration
{

    public function up()
    {
        Schema::create('postproxy_rubrics', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('postproxy_rubrics');
    }

}
