<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('referrals') || Schema::hasColumn('referrals', 'type')) {
            return;
        }
        Schema::table('referrals', function (Blueprint $table) {
            $table->enum('type', ['Referral', 'Manual'])->default('Referral')->after('referring_hospital');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('referrals') || !Schema::hasColumn('referrals', 'type')) {
            return;
        }
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
