<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('customer', 255);
            $table->string('phone', 255);
            $table->timestamp('completed_at')->nullable();
            $table->enum('type', ['online','offline']);
            $table->enum('status', ['Active', 'Completed', 'Canceled']);
            $table->timestamps();
            //$table->bigInteger('user_id')->nullable();

            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
      

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
