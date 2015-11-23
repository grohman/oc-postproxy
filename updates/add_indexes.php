<?php namespace IDesigning\PostProxy\Updates;

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class AddIndexes extends Migration
{

    public function up()
    {
        Schema::table('postproxy_recipients', function(Blueprint $table){
            $table->unique('email');
            $table->index('comment');
        });
    }

    public function down()
    {
        Schema::table('postproxy_recipients', function(Blueprint $table){
            $table->dropUnique('postproxy_recipients_email_unique');
            $table->dropIndex('postproxy_recipients_comment_index');
        });
    }

}
