<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />

  <meta http-equiv="X-UA-Compatible" content="IE=edge" />

<title><?php echo CHtml::encode($this->pageTitle); ?></title>
<link rel="icon" type="image/ico" href="/favicon.ico"/>
<!--[if lt IE 7]> 
        <meta http-equiv="refresh" content="0; url=<?php echo Yii::app()->createUrl('/site/ie6'); ?>" />
<![endif]-->
<!--[if IE 8]>
<link href="<?php echo $this->getAssetsUrl();?>css/ie8.css" rel="stylesheet" type="text/css" />
<![endif]-->
<link rel='stylesheet' type='text/css' href='<?php echo $this->getAssetsUrl();?>css/fullcalendar.print.css' media='print' />

<?php
Yii::app()->clientScript->registerCoreScript('history');

Yii::app()->bootstrap->register();
$jss = array(
  'plugins/jquery/jquery-ui-1.10.1.custom.min.js',
  'plugins/jquery/jquery-migrate-1.2.1.min.js',
  'plugins/jquery/jquery.mousewheel.min.js',
  'plugins/cookie/jquery.cookies.2.2.0.min.js',
  // <!-- 日历 -->
  'plugins/fullcalendar/fullcalendar.min.js',
  'plugins/uniform/uniform.js',
  // <!-- 滚动条 -->
  'plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js',
  // <!-- 弹窗 ，图片 -->
  // 'plugins/fancybox/jquery.fancybox.pack.js',
  // <!-- 消息提示 -->
  'plugins/pnotify/jquery.pnotify.min.js',
  // <!-- 美化按钮 -->
  'plugins/ibutton/jquery.ibutton.min.js',
  
  // 向上滚动的图标
  'plugins/scrollup/jquery.scrollUp.min.js',
  'cookies.js',
  'actions.js?2014-03-10',
  'plugins.js?2014-03-10',
  'settings.js',
  'k-load-select.js',
  //Guid
  'plugins/intro/intro.js',

  'plugins/stepywizard/jquery.stepy.js',
  // 'plugins/validate/jquery.validate.min.js',
);

  $this->regCssFile(array('stylesheets.css','introjs.css','ak.css?2014'))->regScriptFile($jss);

  $scrpitS = array('_ak/js/modernizr.js');
  $cssS = array();

  $path = '_ak/js/plugins/datepicker/';
  $cssS[] = $path.'skin/WdatePicker.css';
  $scrpitS[] = $path.'WdatePicker.js';
  $scrpitS[] = $path.'lang/zh-cn.js';

  $path = '_ak/js/plugins/select2/';
  $cssS[] = $path.'select2.css';
  $scrpitS[] = $path.'select2.min.js';
  $scrpitS[] = $path.'select2_locale_zh.js';

  // 颜色插件
  $path = '_ak/js/plugins/spectrum/';
  $cssS[] = $path.'spectrum.css';
  $scrpitS[] = $path.'spectrum.js';

  if (YII_DEBUG) {
    $scrpitS[] = '_ak/js/jq.common.js';  
  }
  
  Tak::regScriptFile($scrpitS,'static');
  Tak::regCssFile($cssS,'static'); 
?>
<script type="text/javascript">
  var CrmPath = '<?php echo Yii::app()->getBaseUrl();?>/';
</script>
</head>

<body id="ibody" class="<?php echo Yii::app()->user->getState('themeSettings_bg'); ?>" >   
<div class="wrapper<?php echo ' '.Yii::app()->user->getState('themeSettings_style'); if(Yii::app()->user->getState('themeSettings_fixed')) echo ' fixed'; ?>">
  <div class="header">
  <?php
   echo CHtml::tag('a'
    ,array(
      'class'=>'logo'
      ,'href'=>Yii::app()->homeUrl
      ,'title'=>CHtml::encode(Yii::app()->name)
      )
    ,'<span>'.CHtml::encode(Yii::app()->name).'</span>'
    );
  ?>

    <ul class="header_menu">
      <li class="list_icon " <?php if(Yii::app()->user->getState('themeSettings_menu')) echo 'style="display: list-item;"'; ?>><a href="#">&nbsp;</a></li>
      <li class="settings_icon"> <a href="#" class="link_themeSettings">&nbsp;</a>
        <div id="themeSettings" class="popup">
          <div class="head clearfix">
            <div class="arrow"></div>
            <span class="isw-settings"></span> <span class="name">主题设置</span> </div>
          <div class="body settings">
            <div class="row-fluid">
              <div class="span3"><strong>颜色:</strong></div>
              <div class="span9">
                <a class="styleExample active" title="Default style" data-style="">&nbsp;</a> 
                <a class="styleExample silver " title="Silver style" data-style="silver">&nbsp;</a> 
                <a class="styleExample dark " title="Dark style" data-style="dark">&nbsp;</a> 
                <a class="styleExample marble " title="Marble style" data-style="marble">&nbsp;</a> 
                <a class="styleExample red " title="Red style" data-style="red">&nbsp;</a> 
                <a class="styleExample green " title="Green style" data-style="green">&nbsp;</a> 
                <a class="styleExample lime " title="Lime style" data-style="lime">&nbsp;</a> 
                <a class="styleExample purple " title="Purple style" data-style="purple">&nbsp;</a> 
              </div>
            </div>
            <div class="row-fluid">
              <div class="span3"><strong>背景:</strong></div>
              <div class="span9"> <a class="bgExample active" title="Default" data-style="">&nbsp;</a> <a class="bgExample bgCube " title="Cubes" data-style="cube">&nbsp;</a> <a class="bgExample bghLine " title="Horizontal line" data-style="hline">&nbsp;</a> <a class="bgExample bgvLine " title="Vertical line" data-style="vline">&nbsp;</a> <a class="bgExample bgDots " title="Dots" data-style="dots">&nbsp;</a> <a class="bgExample bgCrosshatch " title="Crosshatch" data-style="crosshatch">&nbsp;</a> <a class="bgExample bgbCrosshatch " title="Big crosshatch" data-style="bcrosshatch">&nbsp;</a> <a class="bgExample bgGrid " title="Grid" data-style="grid">&nbsp;</a> </div>
            </div>
            <div class="row-fluid">
              <div class="span3"><strong>固定布局:</strong></div>
              <div class="span9">
                <input type="checkbox" name="settings_fixed" value="1"/>
              </div>
            </div>
            <div class="row-fluid">
              <div class="span3"><strong>隐藏 菜单:</strong></div>
              <div class="span9">
                <input type="checkbox" name="settings_menu" value="1"/>
              </div>
            </div>
          </div>
          <div class="footer">
            <button class="btn link_themeSettings" type="button">关闭</button>
          </div>
        </div>
      </li>
    </ul>
  </div>
  <div class="menu <?php if(Yii::app()->user->getState('themeSettings_menu')) echo 'hidden'; ?>">
    <div class="breadLine">
      <div class="arrow"></div>
      <div class="adminControl active"> 欢迎，
        <?php 
          echo Tak::getManame();
          // echo Tak::getManageid();
        ?> 

      </div>
    </div>
    <div class="admin">
      <div class="image">
      <?php $this->widget('application.components.GoogleQRCode', array(
          'size' => 82,
          'content' => Yii::app()->request->hostInfo.Yii::app()->request->getUrl(),
          'htmlOptions' => array('class' => 'img-polaroid')
      ));
      ?>
      </div>
      <ul class="control">
        <!-- <li><i class="icon-comment"></i> <a href="#<?php echo Yii::app()->createUrl('site/messate');?>">消息</a> <a href="<?php echo $this->createUrl('/site/message')?>" class="caption red">12</a></li> -->
        <li><i class="icon-user"></i><a href="<?php echo $this->createUrl('/site/profile')?>">个人资料</a></li>

        <li id="tak-changepwd" style="position:relative;"><i class="icon-magnet"></i> <a href="<?php echo $this->createUrl('/site/changepwd')?>" class="chage-pwd">修改密码</a>
        </li>
        <li><i class="icon-share-alt"></i> <a href="<?php echo $this->createUrl('/site/logout')?>" class="logout "><span class="red">退出系统</span></a></li>
        <li><i class="icon-share"></i>企业编号: 
        <!--
          <span class="label label-warning"><?php echo Tak::getFormid();?></span>
        -->
        <span href="messages.html" class="caption"><?php echo Tak::getFormid();?></span>

         </li>
      </ul>
      <div class="info"> <span>上一次登录：<?php echo Yii::app()->user->last_login_time;?></span> </div>
    </div>
<?php 
$items = Tak::getMainMenu();          
     $this->widget('application.components.MyMenu',array(
          'itemTemplate'=>'{menu}',
          'activateParents'=>true, //父节点显示
          'itemCssClass' => 'openable',
          'activeCssClass'=>'active',
          'firstItemCssClass'=>'',//第一个
          'lastItemCssClass'=>'',//最后一个
          'htmlOptions'=>array('class'=>'navigation'),
          'encodeLabel' => false, //是否过滤HTML代码
          'submenuHtmlOptions' => array(),
          /*'linkLabelWrapper' => "", //显示内容的标签*/
          'items'=> $items
      ));
?> 
    <div class="dr"><span></span></div>

    <div class="widget-fluid">
      <div id="menuDatepicker"></div>
    </div>
    <div class="dr"><span></span></div>
 
  </div>
  <div class="content <?php if(Yii::app()->user->getState('themeSettings_menu')) echo 'wide'; ?>">

      <!-- breadcrumbs -->
    <div class="breadLine">
      <?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
          'links'=>$this->breadcrumbs,
        )); ?>
      <!-- breadcrumbs -->
    </div>
    <div class="workplace">
      <?php echo $content; ?>
    </div>
  </div>
  <div class="hide">
<?php
 CHtml::tag('iframe',
    array(
        'src'=>Yii::app()->createUrl('/site/appchace'),
        'style'=>'width:0px; height:0px; visibility:hidden; position:absolute; border:none;'
      )
  ,'') ?>
</div>
</div>
<?php
  Tak::showMsg();
?>
<?php Tak::copyright() ?>
</body>
</html>
