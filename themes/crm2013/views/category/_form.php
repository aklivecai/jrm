<?php
    $action = $model->isNewRecord ? 'Create' : 'Update';
?>
<div class="row-fluid">
<div class="span12">
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'mod-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => true,
)); 
    echo $form->errorSummary($model); 

    $parentname = Category::getProductName($model->parentid);

?>
<?php if (!$this->isAjax) :?>
<div class="head clearfix">
    <i class="isw-documents"></i> 
    <h1><?php echo Tk::g(array(
    $action,
    'Creategory'
)); ?></h1>
</div>
<?php endif ?>
<div class="row-fluid">

 <div class="row-form clearfix" style="border-top-width: 0px;">
  <div class="control-group ">
    <label class="control-label" for="Category_parentid">上级分类</label>
    <div class="controls">
                <span class="span10">
                    <div class="row-fluid input-prepend input-append">
                    <?php echo $form->hiddenField($model,'parentid',array('class'=>'sourceField'))?>
                    <input name="popupReferenceModule" type="hidden" value="product">
                    <span class="add-on clearReferenceSelection cursorPointer">
                        <i class='icon-remove-sign' title="清除"></i>
                    </span>
                        <input  name="vendor_id_display" type="text" class="span7" value="<?php echo $parentname?>" placeholder="请选择分类" readonly="readonly" />
                    <span class="add-on relatedPopup cursorPointer">
                        <i class="icon-search relatedPopup" title="Select" ></i>
                    </span>
                    <span class="add-on cursorPointer hide">
                        <i class='icon-plus' title="添加"></i>
                    </span>
                    <span class="help-inline error" id="Category_parentid_em_" style="display: none"></span>
                </div>
        </span>
    </div>
</div>
</div>
<div class="row-form clearfix">                
        <?php
if ($model->isNewRecord&&false) {
    echo $form->textFieldRow($model, 'catename', array(
        'size' => 60,
        'maxlength' => 60
    ));
} else {
    echo $form->textFieldRow($model, 'catename', array(
        'size' => 60,
        'maxlength' => 100
    ));
}
?>
    </div>
    <div class="row-form clearfix">
        <?php echo $form->textFieldRow($model, 'listorder', array(
    'size' => 60,
    'maxlength' => 255
)); ?>
    </div>

<div class="footer tar">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
    'size' => 'large',
    'buttonType' => 'submit',
    'label' => Tk::g($action)
)); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array(
    'size' => 'large',
    'buttonType' => 'reset',
    'label' => Tk::g('Reset')
)); ?>    
</div>
<?php $this->endWidget(); ?>
</div>
</div>