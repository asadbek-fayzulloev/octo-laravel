<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOctoTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('octo_transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_transaction_id');
            $table->integer('user_id');
            $table->integer('booking_id');
            $table->double('price');
            $table->varchar('currency');
            $table->varchar('octo_payment_UUID')->nullable();
            $table->varchar('status')->nullable();
            $table->varchar('octo_pay_url')->nullable();
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
        Schema::dropIfExists('octo_transactions');
    }
}
