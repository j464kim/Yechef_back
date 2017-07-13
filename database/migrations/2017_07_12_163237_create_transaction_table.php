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
			$table->string('charge_id');
			$table->string('currency');
			$table->float('amount');
			$table->float('captured_amount');
			$table->boolean('captured');
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
