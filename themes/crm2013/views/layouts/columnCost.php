<?php
Yii::app()->getClientScript()->registerCoreScript('jquery');
$this->regCssFile('cost/style.css?t=112');
$strScript = sprintf('var CrmPath = "%s/";', Yii::app()->getBaseUrl());
Tak::regScript('', $strScript, CClientScript::POS_HEAD);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title><?php echo true ? Yii::app()->name : CHtml::encode($this->pageTitle); ?></title>
<link rel="icon" type="image/ico" href="/favicon.ico"/>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->getBaseUrl(); ?>/css/tak-printf.css" media="print">
<base target="_self">
</head>
<body>
<div id="content">
    <nav style="display:none">
    <?php
$this->widget('zii.widgets.CMenu', array(
    'items' => array(
        array(
            'label' => '成本核算',
            'url' => array(
                'Create'
            )
        ) ,
        array(
            'label' => '车间管理',
            'url' => array(
                'Workshop'
            )
        ) ,
        array(
            'label' => 'View核算',
            'url' => array(
                'view',
                'id' => '65779604866977208',
            )
        ) ,
        array(
            'label' => '生产',
            'url' => array(
                'production',
                'id' => '65779604866977208',
            )
        ) ,
    )
));
?>
    </nav>
    <?php echo $content; ?>
</div><!-- content -->
</body>
</html>