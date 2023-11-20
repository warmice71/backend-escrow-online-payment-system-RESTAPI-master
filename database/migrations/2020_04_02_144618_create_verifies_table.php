<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerifiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verifies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');            
            $table->char('secretquestion', 100)->nullable();
            $table->char('secretanswer1', 100)->nullable(); 
            $table->char('secretanswer2', 100)->nullable();
            $table->char('secretanswer3', 100)->nullable();
            $table->char('secretanswer4', 100)->nullable();
            $table->char('secretanswer5', 100)->nullable();
            $table->char('secretanswer6', 100)->nullable();
            $table->char('secretanswer7', 100)->nullable();
            $table->char('secretanswer8', 100)->nullable();
            $table->char('secretanswer9', 100)->nullable();
            $table->char('secretanswer10', 100)->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verifies');
    }
}
