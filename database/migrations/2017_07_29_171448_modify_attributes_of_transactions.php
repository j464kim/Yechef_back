<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyAttributesOfTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('transactions', function (Blueprint $table) {
			$table->decimal('buyer_fee', 7, 2)->after('amount');
			$table->decimal('seller_fee', 7, 2)->after('buyer_fee');
			$table->unsignedInteger('kitchen_id')->after('payment_id');
			$table->foreign('kitchen_id')->references('id')->on('kitchens');
			$table->dropColumn('captured');
			$table->renameColumn('amount', 'total');
			$table->renameColumn('refunded', 'released');
			DB::statement("ALTER TABLE transactions MODIFY COLUMN refunded_amount DECIMAL(7,2) AFTER captured_amount");
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('transactions', function (Blueprint $table) {
			$table->dropColumn('buyer_fee');
			$table->dropColumn('seller_fee');
			$table->dropForeign('transactions_kitchen_id_foreign');
			$table->dropColumn('kitchen_id');
			$table->boolean('captured')->default(0);
			$table->renameColumn('total', 'amount');
			$table->renameColumn('released', 'refunded');
		});
    }
}
