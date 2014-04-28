<?php
/**
 * This is the bootstrap file for test application.
 * This file should be removed when the application is deployed for production.
 */

// change the following paths if necessary
// 
// 

         function  forLinux()
        {
        	$arr = array();
                @exec (" ifconfig -a ", $arr);
                 return   $arr;
        }

$yii = dirname(__FILE__).'/../Yii/framework/yii.php';

$config = dirname(__FILE__).'/protected/config/test.php';

// remove the following line when in production mode
// defined('YII_DEBUG') or define('YII_DEBUG',true);

require_once($yii);
// Yii::createWebApplication($config)->run();

Yii::createWebApplication($config);