<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayOutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_outs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('payment_id', 100);
            $table->string('currency', 5);
            $table->float('item_price', 8, 2);
            $table->string('payment_method', 10);
            $table->char('seller_email', 100);
            $table->char('item_name', 100);
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
        Schema::dropIfExists('pay_outs');
    }
}
