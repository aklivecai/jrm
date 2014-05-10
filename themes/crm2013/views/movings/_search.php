<?php /** @var BootActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'search-form',
    'type'=>'search',
    'htmlOptions'=>array('class'=>'well'),
    'action' => Yii::app()->createUrl($this->route),
    'method'=>'get',
)); 

$types = array('-1'=>$model->getAttributeLabel('typeid'));
foreach ($cates as $key => $value) {
	$types[$key] =  $value;
}
  echo $form->dropDownList($model,'typeid',$types);
  echo $form->dropDownList($model,'warehouse_id',Warehouse::toSelects($model->getAttributeLabel('warehouse_id')));
  echo $form->textFieldRow($model,'enterprise',array('size'=>10,'maxlength'=>10));   
 echo $form->textFieldRow($model,'time_stocked',array('size'=>10,'maxlength'=>10,'class'=>'type-date')); 
?>

<?php 
  $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>Tk::g('Search'))); 

$this->widget('bootstrap.widgets.TbButton', array(
    'buttonType' => 'button',
    'label' => Tk::g('More') ,
    'htmlOptions' => array(
        'class' => 'btn-more-serch'
    )
));
?>
<div id="list-more-search" class="hide">
<ul>
  <li>
    <?php echo CHtml::activeLabelEx($model,'numbers')?>:
    <?php echo CHtml::activeTextField($model,'numbers');?>
  </li>
  <li>
    <?php echo CHtml::activeLabelEx($model,'us_launch')?>:
    <?php echo CHtml::activeTextField($model,'us_launch');?>
  </li>
  <li>
    <?php echo CHtml::activeLabelEx($model,'note')?>:
    <?php echo CHtml::activeTextField($model,'note');?>
  </li>
</ul>
</div>
<?php $this->endWidget(); ?>   