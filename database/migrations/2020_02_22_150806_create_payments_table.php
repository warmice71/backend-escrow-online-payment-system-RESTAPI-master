<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('intent_id', 100)->nullable();
            $table->unsignedBigInteger('item_id');
            $table->string('escrow_order_id', 100)->nullable();
            $table->enum('payment_option', ['stripe', 'escrow']);
            $table->string('currency', 5);
            $table->float('amount_paid', 8, 2);
            $table->float('item_price', 8, 2);
            $table->float('amount_received', 8, 2);
            $table->float('commission', 8, 2);
            $table->unsignedBigInteger('seller_id');
            $table->char('seller_email', 100);
            $table->unsignedBigInteger('buyer_id');
            $table->char('buyer_name', 100);
            $table->char('buyer_email', 100);
            $table->boolean('payment_completed')->default(false);
            $table->boolean('payout_initiated')->default(false);
            $table->boolean('transaction_completed')->default(false);
            $table->longText('item_description', 500);
            $table->string('hash_id', 100)->nullable();
            $table->timestamps();
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('buyer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
