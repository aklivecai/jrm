<?php
/* @var $this ProductController */
/* @var $model Product */
/* @var $form bootstrap.widgets.TbActiveForm */
?>
<?php  
	$action = $model->isNewRecord?'Create':'Update';
 $items = Tak::getEditMenu($model->itemid,$model->isNewRecord);
?>
<div class="row-fluid">
<div class="span12">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'product-form',
	 'type'=>'horizontal',
	'enableAjaxValidation'=>false,
)); ?>
<?php echo $form->errorSummary($model); ?>
<div class="head clearfix">
	<i class="isw-documents"></i><h1><?php echo Tk::g(array('Product',$action));?></h1>
<?php 
$this->widget('application.components.MyMenu',array(
      'htmlOptions'=>array('class'=>'buttons'),
      'items'=> $items ,
));
?>      
</div>
<div class="block-fluid">
	<div class="row-form clearfix" >
		<?php echo $form->textFieldRow($model,'name',array('size'=>60,'maxlength'=>100)); ?>
	</div>
 <div class="row-form clearfix">
  <div class="control-group ">
    <label class="control-label">
    		<?php echo $model->getAttributeLabel('typeid')?>
    </label>
    <div class="controls">
                <span class="span10">
                    <div class="row-fluid input-prepend input-append">
                    <?php echo $form->hiddenField($model,'typeid',array('class'=>'sourceField'))?>
                    <input name="popupReferenceModule" type="hidden" value="product">
                    <span class="add-on clearReferenceSelection cursorPointer">
                        <i class='icon-remove-sign' title="清除"></i>
                    </span>
                        <input  name="vendor_id_display" type="text" class="span7" value="" placeholder="请选择" readonly="readonly" />
                    <span class="add-on relatedPopup cursorPointer">
                        <i class="icon-search relatedPopup" title="Select" ></i>
                    </span>
                    <span class="add-on cursorPointer hide" data-url="/">
                        <i class='icon-plus' title="添加"></i>
                    </span>
                    <span class="help-inline error" id="Category_parentid_em_" style="display: none"></span>
                </div>
        </span>
    </div>
</div>
</div>	
	<div class="row-form clearfix" >
		<?php echo $form->textFieldRow($model,'price',array('size'=>60,'maxlength'=>100)); ?>
	</div>
	<div class="row-form clearfix" >
		<?php echo $form->textFieldRow($model,'material',array('size'=>60,'maxlength'=>100)); ?>
	</div>
	<div class="row-form clearfix" >
		<?php echo $form->textFieldRow($model,'spec',array('size'=>60,'maxlength'=>100)); ?>
	</div>
	<div class="row-form clearfix" >
		<?php echo $form->textFieldRow($model,'color',array('size'=>10,'maxlength'=>10)); ?>
	</div>
	<div class="row-form clearfix" >
		<?php echo $form->textFieldRow($model,'unit',array('size'=>10,'maxlength'=>10)); ?>
	</div>
	<div class="row-form clearfix" >
		<?php echo $form->textAreaRow($model,'note',array()); ?>
	</div>

</div>
<div class="footer tar">
    <?php $this->widget('bootstrap.widgets.TbButton', array('size'=>'large','buttonType'=>'submit', 'label'=>Tk::g($action))); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array('size'=>'large','buttonType'=>'reset', 'label'=>Tk::g('Reset'))); ?>    
</div>
<?php $this->endWidget(); ?>
</div>
</div>