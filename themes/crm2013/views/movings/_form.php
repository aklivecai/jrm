<?php
/* @var $this MovingsController */
/* @var $model Movings */
/* @var $form bootstrap.widgets.TbActiveForm */
?>
<?php  $action = $model->isNewRecord?'Entering':'Update';

if ($model->isNewRecord) {
  $model->initak($this->type);
}

$items = Tak::getEditMenu($model->itemid,$model->isNewRecord);

?>

<div class="page-header">
  <h1><?php echo $model->sName?> <small><?php echo Tk::g($this->cates[$model->typeid]);?></small> </h1>
</div>
<div class="row-fluid">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'movings-form',
        'enableAjaxValidation'=>true,
        'enableClientValidation'=>true,
    )); ?>

  <div class="head clearfix">
    <i class="isw-documents"></i>
    <h1><?php echo Tk::g($action);?></h1>
<?php 
$this->widget('application.components.MyMenu',array(
      'htmlOptions'=>array('class'=>'buttons'),
      'items'=> $items ,
));
?>  
  </div>

  <div class="block-fluid clearfix">
    <?php echo $form->hiddenField($model,'typeid'); ?>
    <div class="dr"><span></span></div>
    <?php echo $form->errorSummary($model,null,null,array('class'=>'alert alert-error'));?>
    <div class="span4">
      <div class="block-fluid without-head">
        <div class="toolbar nopadding-toolbar clear clearfix">
          <h4>单据信息</h4>
        </div>
        <div class="row-form clearfix" style="border-top-width: 0px;"> <span class="span3">
        <?php echo $form->labelEx($model,'time'); ?></span> <span class="span9">
        <?php echo $form->dateField($model,'time',array('required'=>'required','size'=>10,'maxlength'=>10,'value'=>($model->time>0?Tak::timetodate($model->time):''))); ?>
        </span> </div>
        <div class="row-form clearfix"> <span class="span3"><?php echo $form->labelEx($model,'numbers'); ?></span> <span class="span9"><?php echo $form->textField($model,'numbers',array('size'=>60,'maxlength'=>100)); ?></span> </div>
        <div class="row-form clearfix"> <span class="span3"><?php echo $form->labelEx($model,'enterprise'); ?></span> <span class="span9"><?php echo $form->textField($model,'enterprise',array('required'=>'required','size'=>60,'maxlength'=>100)); ?></span> </div>
        <div class="row-form clearfix"> <span class="span3"><?php echo $form->labelEx($model,'us_launch'); ?></span> <span class="span9"><?php echo $form->textField($model,'us_launch',array('size'=>60,'maxlength'=>100)); ?></span> </div>
        <div class="row-form clearfix"> <span class="span3"><?php echo $form->labelEx($model,'note'); ?></span> <span class="span9"><?php echo $form->textArea($model,'note',array('size'=>60,'maxlength'=>255)); ?></span> </div>
      </div>
    </div>
    <div class="span7">
      <div class="block-fluid without-head">
        <div class="toolbar nopadding-toolbar clearfix">
          <h4>产品明细</h4>
        </div>
        <table cellpadding="0" cellspacing="0" width="100%" class="table" >
          <thead>
            <tr>
              <th>产品</th>
              <th >规格</th>
              <th >材料</th>
              <th >颜色</th>
              <th width="65">单价</th>
              <th width="50">数量</th>            
              <th width="50">备注</th>
              <th width="30">移除</th>
            </tr>
          </thead>
          <tbody class="not-mpr" id="product-movings">
            <tr id="data-loading">
              <td colspan="8" class="grid-view-loading">...</td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="6">
                    <div>
                            <input type="text" class="sele1ct-product" placeholder="搜索产品" />
                    </div>
              </td>
            <td colspan="2" class="tar">
            <span class="kr">
      <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'link', 'label'=>Tk::g($action))); ?>
      </span>
            </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
</div>
<div class="footer tar">
  <?php $this->widget('bootstrap.widgets.TbButton', array('size'=>'large','buttonType'=>'submit', 'label'=>Tk::g($action))); ?>
  <?php $this->widget('bootstrap.widgets.TbButton', array('size'=>'large','buttonType'=>'reset', 'label'=>Tk::g('Reset'))); ?>
</div>

<?php 
  $this->endWidget(); 
  $this->regScriptFile('k-load-movings.js?t=1', CClientScript::POS_END);


$strProducts =  false;
$products = isset($_POST['Product'])?$_POST['Product']:array();


if (!$products&&$model->itemid>0) {
  $products = ProductMoving::getListByMovingid($model->itemid);
}

if ($products) {
  $strProducts = array();
  foreach ($products as $key => $value) {
    $strProducts[] = array(
        'itemid'=>$key,
        'spec'=>$value['spec'],
        'material'=>$value['material'],
        'number'=>$value['number'],
        'note'=>$value['note'],
        'name'=>$value['name'],
        'price'=>$value['price'],
      );
  }
  $script = 'takson =' ;
  $script .= json_encode($strProducts);  
}  
$script .=";";
Tak::regScript('bodyend',$script,CClientScript::POS_END);
?>
</div>
66849000-2
<!-- 域名已被 musiaiya.com (DNSZone)占用 -->
01:30miao
