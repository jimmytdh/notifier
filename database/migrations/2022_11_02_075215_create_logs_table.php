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
        Schema::create('logs', function (Blueprint $table) {

            $table->id();
            $table->foreignId('referral_id')->constrained()->cascadeOnDelete();
            $table->integer('forwarded_by');
            $table->integer('tagged_by')->nullable();
            $table->string('origin');
            $table->string('destination');
            $table->string('remarks')->nullable();
            $table->timestamp('date_tagged')->nullable()->useCurrentOnUpdate();
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
};
