<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHyperpayTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hyperpay_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('package_id');
            $table->bigInteger('transaction_id');
            $table->string('hyperpay_id');
            $table->string('reference');
            $table->decimal('amount');
            $table->string('result_code');
            $table->string('result_description');
            $table->string('paymentBrand');
            $table->json('customer')->nullable();
            $table->json('billing')->nullable();
            $table->json('card')->nullable();
            $table->json('response');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hyperpay_transactions');
    }
}
