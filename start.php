<?php

use Doctrine\ORM\EntityManager,
	Laravel\Autoloader,
	Laravel\Config,
	Laravel\IoC;

/**
 * Delegate the starting to an event so we can start *after* the application bundle.
 *
 * This gives the application time to override configs before we boot Doctrine.
 */
Event::listen('laravel.started: doctrine', function()
{

	/**
	 * Register the Doctrine namespace with the auto-loader.
	 */
	Autoloader::namespaces(array('Doctrine' => Bundle::path('doctrine').'lib'));

	/**
	 * Register the Symfony namespace if nothing else has registered it.
	 */
	if ( ! isset(Autoloader::$namespaces['Symfony\\']))
	{
		Autoloader::namespaces(array('Symfony' => Bundle::path('doctrine').'lib/Symfony'));
	}

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
	 * Register an EntityManager in the IoC container as an instance.
	 */
	$em = EntityManager::create(array('pdo' => Laravel\Database::connection()->pdo), $config);

	IoC::instance('doctrine::manager', $em);

});