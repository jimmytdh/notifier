<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWardBedNoToReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->string('ward')->nullable()->after('chief_complaint');
            $table->string('bed_no')->nullable()->after('ward');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropColumn(['ward', 'bed_no']);
        });
    }
}
