<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Госты',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'zaq135',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
        'admin',
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
	    'email'=>array(
	        'class'=>'application.extensions.email.Email',
	        'delivery'=>'php', //Will use the php mailing function.  
	        //May also be set to 'debug' to instead dump the contents of the email into the view
	    ),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
            'showScriptName'=>false,
			'rules'=>array(
                array('api/list', 'pattern'=>'api/<id:\d+>', 'verb'=>'GET'),
                array('api/list', 'pattern'=>'api/list', 'verb'=>'GET'),
                array('api/file', 'pattern'=>'api/file/<id:[\d]+>', 'verb'=>'GET'),
                array('api/file', 'pattern'=>'api/file/<name:[A-Za-zА-Яа-я0-9\-\_\(\)\s.]+>', 'verb'=>'GET'),
                array('api/find', 'pattern'=>'api/find/<name:[\w.]+>', 'verb'=>'GET'),
                array('api/mail', 'pattern'=>'api/', 'verb'=>'POST'),
                array('api/signup', 'pattern'=>'api/signup', 'verb'=>'POST'),
                array('api/activation', 'pattern'=>'activation', 'verb'=>'GET'),
                array('api/login', 'pattern'=>'login', 'verb'=>'POST'),
                array('api/forgot', 'pattern'=>'api/forgot', 'verb'=>'POST'),
                '/'=>'/admin',
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
                '<module:\w+>/<controller:\w+>/<action:\w+>/<id:.+>' => '<module>/<controller>/<action>',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
);