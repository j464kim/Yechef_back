<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transactions', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('payment_id');
			$table->foreign('payment_id')->references('id')->on('payments');
			$table->string('charge_id');
			$table->string('currency');
			$table->decimal('amount', 7, 2);
			$table->decimal('captured_amount', 7, 2);
			$table->boolean('captured');
			$table->decimal('refunded_amount', 7, 2);
			$table->boolean('refunded');
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('transactions');
	}
}
