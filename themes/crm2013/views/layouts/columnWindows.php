<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
<link rel="icon" type="image/ico" href="favicon.ico"/>

<script type="text/javascript">
  var CrmPath = '<?php echo Yii::app()->getBaseUrl(); ?>/';
</script>
<?php
Yii::app()->bootstrap->register();
$this->regCssFile(array(
    'window.css',
    'ak.css?2014'
))->regScriptFile('k-load.js');
?>
<script type="text/javascript" src="<?php echo $this->getAssetsUrl(); ?>js/lib.js?>"></script>
<!--[if lt IE 9]>
<link href="<?php echo $this->getAssetsUrl(); ?>css/ie8.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--[if lt IE 10]> 
<script type="text/javascript" src="<?php echo $this->getAssetsUrl(); ?>js/ie.js?>"></script>
<![endif]-->

<base target="_self">

</head>
<body>
<div id="content">
	<?php echo $content; ?>
</div><!-- content -->
<script type="text/javascript">
/**
 * 
 * @authors aklivecai (aklivecai@gmail.com)
 * @date    2014-06-06 08:34:12
 * @version $Id$
 */

jQuery(function($) {
	var btnMore = $('.btn-more-serch');
    btnMore.on('click', function() {
    	btnMore.next('#list-more-search').toggleClass('hide');
    });
});
</script>
</body>
</html>