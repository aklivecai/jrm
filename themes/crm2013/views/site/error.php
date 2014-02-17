<?php
  if (isset($error)&&$error['code']) {
    $this->pageTitle =  ' - ' .$error['code'];
  }
?>
<div class="errorPage">
  <p class="name">
    <?php echo $code; ?></p>
  <p class="description">
    <?php echo CHtml::encode($message); ?></p>
  <p>
    <?php 
        if ($code!='202') {
       $url = $url?$url:Yii::app()->homeUrl;
       $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'linkurl', 'label'=>'回到主页','type'=>'warning','htmlOptions'=>array('href'=>$url)));
        if (Yii::app()->request->urlReferrer) {
          $url = Yii::app()->request->urlReferrer;
          $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'linkurl', 'label'=>'返回上一页','type'=>'danger','htmlOptions'=>array('href'=>$url)));
        }
      if (!YII_DEBUG) {
        $cl = Yii::app()->getClientScript();
        $cl->registerMetaTag("8;url=$url",null,'refresh');
        $cl->registerScript('1', "setTimeout(function(){ window.location = \"".$url."\";} ,8 * 1000);");
      }
   }
        ?>
  </p>
  <hr />
  <h4><?php echo Yii::app()->params['help'];?></h4>
</div>