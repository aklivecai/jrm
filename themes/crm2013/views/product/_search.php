<?php /** @var BootActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'search-form',
    'type'=>'search',
    'htmlOptions'=>array('class'=>'well'),
    'action' => Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>


                <span class="span2">
                    <div class="row-fluid input-prepend input-append">
                    <?php echo $form->hiddenField($model,'typeid',array('class'=>'sourceField'))?>
                      <input name="popupReferenceModule" type="hidden" value="product">
                    <span class="add-on clearReferenceSelection cursorPointer">
                        <i class='icon-remove-sign' title="清除"></i>
                    </span>
                        <input  name="vendor_id_display" type="text" class="span7" value="" placeholder="请选择分类" readonly="readonly" />
                    <span class="add-on relatedPopup cursorPointer">
                        <i class="icon-search relatedPopup" title="请选择分类" ></i>
                    </span>
                </div>
        </span>
<?php 
  echo $form->textFieldRow($model,'name',array('size'=>10,'maxlength'=>10)); 
?>

<?php 
  $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>Tk::g('Search'))); 
  $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'button', 'label'=>Tk::g('Reset'),'htmlOptions'=>array('class'=>'btn-reset'))); 
  echo CHtml::button(Tk::g('Reset'),array('type'=>'reset','class'=>'hide'));
?>
<?php $this->endWidget(); ?>   