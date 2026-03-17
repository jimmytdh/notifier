<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErrorLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('error_logs')) {
            return;
        }
        Schema::create('error_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('userid');
            $table->text('message');
            $table->timestamp('date_created')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('error_logs');
    }
}
