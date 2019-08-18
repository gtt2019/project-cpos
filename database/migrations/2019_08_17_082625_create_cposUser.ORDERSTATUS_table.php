<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCposUser.ORDERSTATUSTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cposUser.ORDERSTATUS', function(Blueprint $table)
		{
			$table->string('companyid', 256)->nullable();
			$table->char('active', 1)->nullable();
			$table->dateTime('lastupdated')->nullable();
			$table->string('statusid', 256)->nullable();
			$table->string('ordstatus', 256)->nullable();
			$table->unique(['companyid','active','statusid'], 'CI_ORDERSTATUS');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cposUser.ORDERSTATUS');
	}

}
