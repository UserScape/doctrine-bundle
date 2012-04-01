<?php

return array(

	// The path to your application's models
	'models' => array(
		'namespace'     => 'Entity',
		'directory'     => path('app').'models',
	),

	// Doctrine proxy class configuration
	'proxy' => array(
		'auto_generate' => true,
		'namespace'     => 'Entity\\Proxy',
		'directory'     => path('app').'models'.DS.'proxies',
	),

);