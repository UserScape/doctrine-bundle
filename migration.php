<?php namespace Doctrine;

use Laravel\IoC, Laravel\Bundle, Laravel\Database as DB;

abstract class Migration {

	/**
	 * The Doctrine Entity Mananger.
	 *
	 * @var Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * The Doctrine Schema Mangager.
	 *
	 * @var Doctrine\DBAL\Schema\AbstractSchemaManager
	 */
	protected $sm;

	/**
	 * The current database schema ready for changes.
	 *
	 * @var Doctrine\DBAL\Schema\Schema
	 */
	protected $schema;

	/**
	 * The original schema before any changes were made.
	 *
	 * @var Doctrine\DBAL\Schema\Schema
	 */
	protected $current;

	/**
	 * Create a new HelpSpot Migration instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		Bundle::start('doctrine');

		$this->em = IoC::resolve('doctrine::manager');

		$this->sm = $this->em->getConnection()->getSchemaManager();
	}

	/**
	 * Execute the migration "up" command.
	 *
	 * @return void
	 */
	public function up()
	{
		// First we need to get the current databsae schema and then make
		// a clone of it that can be modified by the migration and then
		// compared to the original schema using Doctrine.
		$this->current = $this->sm->createSchema();

		$this->schema = clone $this->current;

		// The change method will modify the schema object to alter the
		// schema nad then once it has run we can get the "difference"
		// SQL from doctrine and run it against the database.
		$this->change();

		$this->run($this->get_difference($this->current, $this->schema));

		// After a migration has run, we serialize the original schema
		// and store it in the database so we know how to rollback
		// the changes that were made by this migration.
		$this->save_schema($this->current);
	}

	/**
	 * Execute the migration "down" command.
	 *
	 * @return void
	 */
	public function down()
	{
		// When reversing a migration, we need to pull in the schema
		// as it existed before this migration was run. We can then
		// compare that schema to the current to rollback.
		$this->previous = $this->get_previous();

		$this->current = $this->sm->createSchema();

		$this->run($this->get_difference($this->current, $this->previous));

		$this->delete_schema();
	}

	/**
	 * Get the schema state from before this migration ran.
	 *
	 * @return Schema
	 */
	protected function get_previous()
	{
		$connection = $this->em->getConnection();

		$row = DB::table('laravel_schema')->where('name', '=', get_class($this))->first();

		return unserialize($row->previous);
	}

	/**
	 * Get an array of the SQL needed to migrate from original to current schema.
	 *
	 * @param  Doctrine\DBAL\Schema\Schema  $from_schema
	 * @param  Doctrine\DBAL\Schema\Schema  $to_schema
	 * @return array
	 */
	protected function get_difference($from_schema, $to_schema)
	{
		return $from_schema->getMigrateToSql($to_schema, $this->get_platform());
	}

	/**
	 * Run an array of SQL statements against the connection.
	 *
	 * @param  array  $sql
	 * @return void
	 */
	protected function run($queries)
	{
		foreach ($queries as $query)
		{
			$this->em->getConnection()->query($query);
		}
	}

	/**
	 * Store the pre-migration schema in the database.
	 *
	 * @param  Doctrine\DBAL\Schema\Schema
	 * @return void
	 */
	protected function save_schema($schema)
	{
		$data = array('name' => get_class($this), 'previous' => serialize($schema));

		DB::table('laravel_schema')->insert($data);
	}

	/**
	 * Drop the pre-migration schema for the current migration.
	 *
	 * @return void
	 */
	protected function delete_schema()
	{
		DB::table('laravel_schema')->where('name', '=', get_class($this))->delete();
	}

	/**
	 * Get the abstract database platform.
	 *
	 * @return Doctrine\DBAL\Platforms\AbstractPlatform
	 */
	protected function get_platform()
	{
		return $this->em->getConnection()->getDatabasePlatform();
	}

}