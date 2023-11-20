<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('item_name', 100);
            $table->float('amount', 8, 2);
            $table->char('buyer_name', 100);
            $table->unsignedBigInteger('seller_id');
            $table->char('seller_email', 100);
            $table->char('seller_country', 50);
            $table->char('seller_phone', 15)->nullable();
            $table->char('seller_currency', 10);
            $table->char('seller_flag', 150)->nullable();
            $table->char('connection_channel', 50);
            $table->longText('description', 500);
            $table->char('cover_photo', 100)->nullable();
            $table->char('serial_no', 100)->nullable();
            $table->char('model_no', 100)->nullable();
            $table->char('imei_first', 10)->nullable();
            $table->char('imei_last', 10)->nullable();            
            $table->timestamps();
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
