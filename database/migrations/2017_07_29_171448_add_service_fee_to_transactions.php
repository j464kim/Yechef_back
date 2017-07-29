<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddServiceFeeToTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('transactions', function (Blueprint $table) {
			$table->decimal('service_fee', 7, 2)->after('amount');
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
			$table->dropColumn('service_fee');
		});
    }
}
