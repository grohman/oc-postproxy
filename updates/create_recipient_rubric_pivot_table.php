<?php namespace IDesigning\PostProxy\Updates;

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class CreateRecipientRubricPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postproxy_recipient_rubric', function (Blueprint $table) {
            $table->integer('rubric_id')->unsigned()->index();
            $table->bigInteger('recipient_id')->unsigned()->index();
            $table->timestamps();

            $table->primary(['rubric_id', 'recipient_id']);
            $table->foreign('rubric_id')->references('id')->on('postproxy_rubrics')->onDelete('cascade');
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
        Schema::dropIfExists('postproxy_recipient_rubric');
    }
}
