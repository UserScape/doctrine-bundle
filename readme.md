## Installation

### Artisan

	php artisan bundle:install doctrine

### Bundle Registration

Add the following to your **application/bundles.php** file:

	'doctrine' => array('auto' => true),

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