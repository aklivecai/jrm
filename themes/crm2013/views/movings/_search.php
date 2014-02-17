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
  echo $form->textFieldRow($model,'enterprise',array('size'=>10,'maxlength'=>10)); 

  
 echo $form->textFieldRow($model,'time_stocked',array('size'=>10,'maxlength'=>10,'class'=>'type-date')); 
?>

<?php 
  $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>Tk::g('Search'))); 
  $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'button', 'label'=>Tk::g('Reset'),'htmlOptions'=>array('class'=>'btn-reset'))); 
  echo CHtml::button(Tk::g('Reset'),array('type'=>'reset','class'=>'hide'));
?>
<?php $this->endWidget(); ?>   