<?php

use Doctrine\ORM\EntityManager,
	Laravel\Autoloader,
	Laravel\Config,
	Laravel\IoC;

require __DIR__.'/lib/Doctrine/ORM/Tools/Setup.php';

Doctrine\ORM\Tools\Setup::registerAutoloadGit(__DIR__);

/**
 * Laravel Configuration Loader
 *
 * This is required when using the CLI.
 */
Laravel\Event::listen(Laravel\Config::loader, function($bundle, $file)
{
	return Laravel\Config::file($bundle, $file);
});

/**
 * Delegate the starting to an event so we can start *after* the application bundle.
 *
 * This gives the application time to override configs before we boot Doctrine.
 */
Laravel\Event::listen('laravel.started: doctrine', function()
{

	/**
	 * Resolve the cache provider implementation from the IoC container.
	 */
	if (IoC::registered('doctrine::cache.provider'))
	{
		$cache = IoC::resolve('doctrine::cache.provider');
	}
	else
	{
		$cache = new Doctrine\Common\Cache\ArrayCache;
	}

	/**
	 * Register the cache provider with the Doctrine configuration.
	 */
	$config = new Doctrine\ORM\Configuration;

	$config->setMetadataCacheImpl($cache);

	$config->setQueryCacheImpl($cache);

	$config->setResultCacheImpl($cache);

	/**
	 * Resolve and register the meta-data driver.
	 */
	if (IoC::registered('doctrine::metadata.driver'))
	{
		$driverImpl = IoC::resolve('doctrine::metadata.driver', array($config));
	}
	else
	{
		$driverImpl = $config->newDefaultAnnotationDriver(Config::get('doctrine::config.models'));
	}

	$config->setMetadataDriverImpl($driverImpl);

	/**
	 * Register the proxy configuration with Doctrine.
	 */
	$config->setProxyDir(Config::get('doctrine::config.proxy.directory'));

	$config->setProxyNamespace(Config::get('doctrine::config.proxy.namespace'));

	$config->setAutoGenerateProxyClasses(Config::get('doctrine::config.proxy.auto_generate'));

	/**
	 * Register an autoloader for the model classes
	 */
	$loader = new Doctrine\Common\ClassLoader(Config::get('doctrine::config.models.namespace'), Config::get('doctrine::config.models.directory'));

	$loader->register();

	/**
	 * Register an EntityManager in the IoC container as an instance.
	 */
	$em = EntityManager::create(array('pdo' => Laravel\Database::connection()->pdo), $config);

	IoC::instance('doctrine::manager', $em);

});
