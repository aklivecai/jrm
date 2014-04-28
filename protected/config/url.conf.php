<?php
// return array();
return array(
    'urlFormat' => 'path',
    'showScriptName' => false,
    'caseSensitive' => false,
    'rules' => array(
        '<controller:\w+>/<id:\d+>'=>'<controller>/view',
        '<controller:\w+>/<id:[a-zA-Z0-9-]{30,}>' => '<controller>/view',
        '<controller:\w+>/<action:\w+>/<id:[a-zA-Z0-9-]{30,}>' => '<controller>/<action>',
        // '<controller:\w+>/id-<id:\w+>'=>'<controller>/view',
        // '<controller:permission>/<id:\S+>'=>'permission/view',
        
        '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
        'juren/<controller:\w+>/<id:\d+>' => 'juren/<controller>/view',
        'juren/<controller:\w+>/<action:\w+>' => 'juren/<controller>/<action>',
    ) ,
);
