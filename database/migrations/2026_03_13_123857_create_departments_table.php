<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('departments')) {
            return;
        }
        Schema::create('departments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('department')->unique();
            $table->string('code')->unique();
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
        Schema::dropIfExists('departments');
    }
}
