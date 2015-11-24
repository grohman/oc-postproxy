<?php namespace IDesigning\PostProxy\Updates;

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class AddUnsubscribedLabel extends Migration
{

    public function up()
    {
        Schema::table('postproxy_channel_recipient', function(Blueprint $table){
            $table->boolean('is_unsubscribed')->default(0)->index()->after('recipient_id');
        });
        Schema::table('postproxy_recipient_rubric', function(Blueprint $table){
            $table->boolean('is_unsubscribed')->default(0)->index()->after('recipient_id');
        });
    }

    public function down()
    {
        Schema::table('postproxy_channel_recipient', function(Blueprint $table){
            $table->dropColumn('is_unsubscribed');
        });
        Schema::table('postproxy_recipient_rubric', function(Blueprint $table){
            $table->dropColumn('is_unsubscribed');
        });
    }

}
