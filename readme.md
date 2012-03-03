## Installation

### Artisan

	php artisan bundle:install doctrine

### Bundle Registration

Add the following to your **application/bundles.php** file:

	'doctrine' => array(
		'auto' => true,
		'autoloads' => array(
			'map' => array(
				'Doctrine\Migration' => '(:bundle)/migration.php',
			),
		),
	),

### Doctrine Migration Wrapper

This bundle includes a custom **Doctrine\Migration** class that integrates with the Laravel migration CLI. If you wish
to use this class, be sure to run the following command before writing Doctrine migrations:

	php artisan migrate doctrine

This will create a special table on your database used to store Doctrine schema information. Now you can extend this
class in your migrations like so:

	class Create_Users_Table extends Doctrine\Migration {

		//

	}

When writing Doctrine migrations, you do not need to create an "up" and "down" method. You only need to create a
"change" method which should be the equivalent of the "up" action. The Doctrine migration will intelligently
reverse this change command when rolling back migrations!

To create a Doctrine migration, you can use the following task:

	php artisan doctrine::migrate:make migration_name

In your Doctrine migrations, you may work on your database schema using the Doctrine schema building tools.
For more information, consult the [Docrine schema documentation](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/schema-representation.html).

	class Create_Users_Table extends Doctrine\Migration {

		public function change()
		{
			$table = $this->schema->createTable('users');

			$table->addColumn('id', 'integer', array('autoincrement' => true));

			$table->addColumn('name', 'string');
		}

	}

> **Note:** You can run your Doctrine migrations with the usual "php artisan migrate" command.

### Doctrine CLI

Copy the **doctrine** file from the bundle's directory into your Laravel base directory (the same directory as Artisan).

Once you have done this test the CLI by running:

	php doctrine

## Configuration

### Where To Config?

Since the Doctrine bundle uses its configuration in it's **start.php** file, you should set any Doctrine configuration items within the "start" event for the bundle. A good place to do this is in your **application/start.php** file. Here's what it should look like:

	Event::listen('laravel.started: doctrine', function()
	{
		// Set your Doctrine configuration here!
	});

### Caching

By default, the bundle is configured to use the ArrayCache provider, which is suited for development; however, you will want to change this to another provider implementation for production. In addition to the Doctrine providers, I have also included a **FileCache** provider for those who do not have a memory-based cache such as APC available.

The cache provider is resolved out of the Laravel IoC container. If you would like to use something other than the ArrayCache, simply register **doctrine::cache.provider** in the IoC container:

**Registering a FileCache provider in the IoC container:**

	IoC::register('doctrine::cache.provider', function($config)
	{
		return new Doctrine\Common\Cache\FileCache(path('storage').'cache/doctrine.metadata');
	});

### Models & Proxies

The bundle also uses sensible defaults for your **models** and **proxies**. The model directory is set to **application/models** and the proxy directory is set to **application/models/proxies**. Of course, you can change both:

**Setting the Doctrine model directory:**

	Config::set('doctrine::config.models', $path);

**Setting the Doctrine proxy directory:**

	Config::set('doctrine::config.proxy.directory', $path);

The **proxy namespace** is defaulted to **Entity\Proxy**; however, you may change it:

**Setting the Doctrine proxy namespace:**

	Config::set('doctrine::config.proxy.namespace', $namespace);

By default, proxy classes are set to auto-generate. It is strongly encouraged you set this to **false** in production and generate your proxies once through the Doctrine CLI. Review the Doctrine documentation on proxy classes for more information.

**Setting proxy auto-generation to false:**

	Config::set('doctrine::config.proxy.auto_generate', false);

## License

Doctrine is licensed under the LGPL (GNU Lesser General Public License). For more information, consult the "license.txt" file within the "lib" directory.

For more information about Doctrine, consult the official [Doctrine website](http://www.doctrine-project.org/).