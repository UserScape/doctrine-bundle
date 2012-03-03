<?php use Laravel\Database\Schema;

class Doctrine_Create_Schema_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('laravel_schema', function($table)
		{
			$table->string('name')->primary();
			$table->text('previous');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('laravel_schema');
	}

}