<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePassengersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passengers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_detail_id')->unsigned();
            $table->foreign('order_detail_id')->references('id')->on('order_details')->onUpdate('cascade')->onDelete('cascade');
            $table->string('type');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('title');
            $table->string('identity_no');
            $table->date('birthdate');
            $table->string('adult_index')->nullable();
            $table->string('passport_no')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('passport_issuing_country')->nullable();
            $table->string('passport_nationality')->nullable();
            $table->string('check_in_baggage')->nullable();
            $table->string('check_in_baggage_return')->nullable();
            $table->integer('check_in_baggage_size')->nullable();
            $table->integer('check_in_baggage_size_return')->nullable();
            $table->date('passport_issued_date')->nullable();
            $table->string('birth_country')->nullable();
            $table->string('ticket_number')->nullable();
            $table->string('check_in_baggage_unit')->nullable();
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
        Schema::dropIfExists('passengers');
    }
}
