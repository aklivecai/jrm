<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<!--[if IE]>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
<![endif]-->
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
<link rel="icon" type="image/ico" href="favicon.ico"/>
<?php
  Yii::app()->bootstrap->register();
    $this->regCssFile('window.css');
?>
<base target="_self">
</head>
<body>
<div id="content">
	<?php echo $content; ?>
</div><!-- content -->
</body>
</html>