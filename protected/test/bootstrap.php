<?php
$yii  = dirname(__FILE__).'/../../../Yii/framework/yii.php';
$config = dirname(__FILE__).'/../config/test.php';
require_once($yii);
require_once(dirname(__FILE__)./'WebTestCae.php');
Yii::createWebApplication($config);