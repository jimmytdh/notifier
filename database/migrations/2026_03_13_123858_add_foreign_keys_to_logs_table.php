<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('logs') || !Schema::hasTable('referrals')) {
            return;
        }

        $constraintExists = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'logs')
            ->where('CONSTRAINT_NAME', 'logs_referral_id_foreign')
            ->exists();

        if ($constraintExists) {
            return;
        }

        Schema::table('logs', function (Blueprint $table) {
            $table->foreign(['referral_id'])->references(['id'])->on('referrals')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropForeign('logs_referral_id_foreign');
        });
    }
}
