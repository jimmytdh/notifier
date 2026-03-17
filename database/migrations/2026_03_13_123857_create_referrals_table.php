<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('referrals')) {
            return;
        }
        Schema::create('referrals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('userid');
            $table->string('firebase_key')->unique();
            $table->string('patient');
            $table->string('gender');
            $table->integer('age');
            $table->text('diagnosis')->nullable();
            $table->text('chief_complaint')->nullable();
            $table->string('department')->nullable();
            $table->string('referring_hospital');
            $table->string('status')->nullable();
            $table->timestamp('date_updated')->useCurrentOnUpdate()->nullable();
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
        Schema::dropIfExists('referrals');
    }
}
