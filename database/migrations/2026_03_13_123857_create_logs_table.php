<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('logs')) {
            return;
        }
        Schema::create('logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('referral_id')->index('logs_referral_id_foreign');
            $table->integer('forwarded_by');
            $table->integer('tagged_by')->nullable();
            $table->string('origin');
            $table->string('destination');
            $table->string('remarks')->nullable();
            $table->timestamp('date_tagged')->useCurrentOnUpdate()->nullable();
            $table->timestamp('date_forwarded')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
