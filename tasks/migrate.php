<?php

use Laravel\Str;
use Laravel\File;
use Laravel\Bundle;

class Doctrine_Migrate_Task {

	/**
	 * Create a new Doctrine migration.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function make($arguments)
	{
		if (count($arguments) == 0)
		{
			throw new \Exception("I need to know what to name the migration.");
		}

		list($bundle, $migration) = Bundle::parse($arguments[0]);

		// The migration path is prefixed with the date timestamp, which
		// is a better way of ordering migrations than a simple integer
		// incrementation, since developers may start working on the
		// next migration at the same time unknowingly.
		$prefix = date('Y_m_d_His');

		$path = Bundle::path($bundle).'migrations'.DS;

		// If the migration directory does not exist for the bundle,
		// we will create the directory so there aren't errors when
		// when we try to write the migration file.
		if ( ! is_dir($path)) mkdir($path);

		$file = $path.$prefix.'_'.$migration.EXT;

		File::put($file, $this->stub($bundle, $migration));

		echo "Great! New migration created!";

		// Once the migration has been created, we'll return the
		// migration file name so it can be used by the task
		// consumer if necessary for futher work.
		return $file;
	}	

	/**
	 * Get the stub migration with the proper class name.
	 *
	 * @param  string  $bundle
	 * @param  string  $migration
	 * @return string
	 */
	protected function stub($bundle, $migration)
	{
		$stub = File::get(Bundle::path('doctrine').'migration_stub'.EXT);

		$prefix = Bundle::class_prefix($bundle);

		// The class name is formatted simialrly to tasks and controllers,
		// where the bundle name is prefixed to the class if it is not in
		// the default "application" bundle.
		$class = $prefix.Str::classify($migration);

		return str_replace('{{class}}', $class, $stub);
	}

}