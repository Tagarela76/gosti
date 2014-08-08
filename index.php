<?php

// change the following paths if necessary
$yii=dirname(__FILE__).'/lib/framework/yii.php';
//$config=dirname(__FILE__).'/protected/config/main.php';
require_once($yii);
// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);


$commonConfig = require_once dirname(__FILE__).'/protected/config/main.php';
$commonLocalConfig = require_once dirname(__FILE__).'/protected/config/main-local.php';
$map = new CMap();
$map->mergeWith($commonConfig);
$map->mergeWith($commonLocalConfig);

require_once($yii);
Yii::createWebApplication($map->toArray())->run();
