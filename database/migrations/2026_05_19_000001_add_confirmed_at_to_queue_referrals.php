<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfirmedAtToQueueReferrals extends Migration
{
    public function up()
    {
        Schema::table('queue_referrals', function (Blueprint $table) {
            $table->timestamp('confirmed_at')->nullable()->after('remarks');
        });
    }

    public function down()
    {
        Schema::table('queue_referrals', function (Blueprint $table) {
            $table->dropColumn('confirmed_at');
        });
    }
}
