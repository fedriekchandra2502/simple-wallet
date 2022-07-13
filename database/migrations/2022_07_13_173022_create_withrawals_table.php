<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withrawals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('withdrawn_by')->references('id')->on('users');
            $table->string('status');
            $table->unsignedBigInteger('amount');
            $table->uuid('reference_id')->unique();
            $table->timestamp('withdrawn_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withrawals');
    }
};
