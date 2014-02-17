<?php
$log = array(
    array(
        'class'=>'CFileLogRoute',
        'levels'=>'error, warning',
    )
);
if( YII_DEBUG )
{
    $log_debug = array(
        'class'=>'XWebDebugRouter',
        'config'=>'alignLeft, opaque, runInDebug, fixedPos, collapsed, yamlStyle',
        'levels'=>'error, warning, trace, profile, info',
        'allowedIPs'=>array(
            '127.0.0.1',
            '192.168.1.201X',
        ),
    );

    $log[] = $log_debug;
}    

return $log;

