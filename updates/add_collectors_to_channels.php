<?php namespace IDesigning\PostProxy\Updates;

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class AddCollectorsToChannels extends Migration
{

    public function up()
    {
        Schema::table('postproxy_channels', function(Blueprint $table){
            $table->text('collectors')->nullable()->after('options');
        });
    }

    public function down()
    {
        Schema::table('postproxy_channels', function(Blueprint $table){
            $table->dropColumn('collectors');
        });
    }
}
