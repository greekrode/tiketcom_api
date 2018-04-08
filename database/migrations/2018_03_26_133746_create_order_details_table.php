<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('cascade');
            $table->string('uri');
            $table->decimal('order_price', 22, 3)->default(0);
            $table->string('order_name');
            $table->string('order_name_detail');
            $table->string('airlines_name');
            $table->string('flight_number');
            $table->decimal('price_adult', 22, 3)->default(0);
            $table->decimal('price_child', 22, 3)->default(0);
            $table->decimal('price_infant', 22, 3)->default(0);
            $table->date('flight_date');
            $table->time('departure_time');
            $table->time('arrival_time');
            $table->decimal('baggage_fee', 22, 3)->default(0);
            $table->string('departure_airport_name');
            $table->string('departure_city_name');
            $table->char('departure_city', 3);
            $table->string('arrival_airport_name');
            $table->string('arrival_city_name');
            $table->char('arrival_city', 3);
            $table->string('airlines_photo');
            $table->decimal('price', 22, 3)->default(0);
            $table->decimal('tax_and_charge', 22, 3)->default(0);
            $table->decimal('subtotal_and_charge', 22, 3)->default(0);
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
        Schema::dropIfExists('order_details');
    }
}
