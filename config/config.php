<?php

return array(

	// The path to your application's models
	'models' => path('app').'models',

	// Doctrine proxy class configuration
	'proxy' => array(
		'auto_generate' => true,
		'namespace'     => 'Application\\Model\\Proxy',
		'directory'     => path('app').'models'.DS.'proxies',
	),

);