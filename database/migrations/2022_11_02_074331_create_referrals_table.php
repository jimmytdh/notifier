<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->integer('userid');
            $table->string('firebase_key')->unique();
            $table->string('patient');
            $table->string('gender');
            $table->integer('age');
            $table->text('diagnosis');
            $table->text('chief_complaint');
            $table->string('department');
            $table->string('referring_hospital');
            $table->string('status')->nullable();
            $table->timestamp('date_updated')->nullable()->useCurrentOnUpdate();
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
};
