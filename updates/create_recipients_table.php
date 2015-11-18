<?php namespace IDesigning\PostProxy\Updates;

use DB;
use Schema;
use October\Rain\Database\Updates\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRecipientsTable extends Migration
{

    public function up()
    {
        Schema::create('postproxy_recipients', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('postproxy_recipients');
    }

}
