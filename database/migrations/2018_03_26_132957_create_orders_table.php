<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('total_price', 22, 3)->default(0);
            $table->decimal('total_tax', 22, 3)->default(0);
            $table->decimal('total_without_tax', 22, 3)->default(0);
            $table->decimal('count_installment', 22, 3)->default(0);
            $table->decimal('discount', 22, 3)->default(0);
            $table->decimal('discount_amount', 22, 3)->default(0);
            $table->boolean('trip');
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
        Schema::dropIfExists('orders');
    }
}
