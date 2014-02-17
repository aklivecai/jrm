<?php
/* @var $this OrderController */
/* @var $model Order */

$this->breadcrumbs=array(
	Tk::g('Order')=>array('admin'),
	Tk::g('Admin'),
);
$items = Tak::getListMenu();
?>
<div class="row-fluid">
	<div class="span12">
	<div class="head clearfix">
        <div class="isw-grid"></div>
        <h1><?php echo Tk::g('Order')?></h1>     
	</div>	

<div class="block-fluid clearfix">
<?php /** @var BootActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'search-form',
    'type'=>'search',
    'htmlOptions'=>array('class'=>'well'),
    'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
 

<?php 
  echo $form->dropDownList($model,'status',Order::getSearchStatus()); 
?>

<?php 
  echo $form->textFieldRow($model,'add_time',array('size'=>10,'maxlength'=>10,'class'=>'type-date')); 
?>

<?php echo $form->textFieldRow($model, 'itemid', array('class'=>'input-medium', 'prepend'=>'<i class="icon-search"></i>')); ?>

<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Go')); ?>

<hr />


<?php 
if (YII_DEBUG) {
	$this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'link', 'label'=>'生成测试订单','url'=>array('TestOrder'),'size'=>'large')); 
}
?> 
<?php $this->endWidget(); ?>

<?php 

$listOptions = Tak::gredViewOptions(false);
$listOptions['dataProvider'] = $model->search();
$listOptions['columns'] = array(
		array(
			 'class'=>'bootstrap.widgets.TbButtonColumn'
			  , 'template'=>' <div style="font-weight: bold;text-align: center;line-height:55px;">{updates}</div> '/*<br />{addproduct} <br />{addorderinfo} <br />{addorderflow}*/
			  ,'buttons'=>array(			  	
                    'updates' => array
                    (
                        'label'=>'马上处理',
                         'url'=>'$data->getLinkUP()',
                         'options'=>array('style'=>'color: red;')
                    ),
                    'addproduct' => array
                    (
                        'label'=>'添加商品',
                         'url'=>'Yii::app()->createUrl("OrderProduct/create", array("OrderProduct[order_id]"=>$data->primaryKey,"OrderProduct[fromid]"=>$data->fromid))',
                    ),
                    'addorderinfo' => array
                    (
                        'label'=>'添加订单详情',
                         'url'=>'Yii::app()->createUrl("OrderInfo/create", array("OrderInfo[itemid]"=>$data->primaryKey))',
                    ),
                    'addorderflow' => array
                    (
                        'label'=>'添加流程',
                         'url'=>'Yii::app()->createUrl("OrderFlow/create", array("OrderFlow[order_id]"=>$data->primaryKey))',
                    ),
			  )
			  ,'header' => CHtml::dropDownList('pageSize'
					,Yii::app()->user->getState('pageSize')
					,TakType::items('pageSize')
					,array(
						'onchange'=>"$.fn.yiiGridView.update('list-grid',{data:{setPageSize: $(this).val()}})", 
					)
			  )
			  ,'htmlOptions'=>array('style'=>'width: 85px')
		),		
		array(
			'name'=>'itemid'
			,'headerHtmlOptions'=>array('style'=>'width: 120px')
		),
		array(
			'name'=>'订单产品'
			,'type'=>'raw'
			,'value'=>'$data->wProducts()'

		),
		array(
			'name'=>'total'
			,'headerHtmlOptions'=>array('style'=>'width: 85px')
			,'value'=>'Tak::format_price($data->total)'
			,'htmlOptions'=>array('class'=>'price-strong')
		),
		array(
			'name'=>'add_time'
			,'type'=>'raw'
			,'sortable'=>false
		  	,'header' => CHtml::dropDownList('dt'
				, isset($_GET['dt'])?$_GET['dt']:''
				, Order::getSearchTime()
				, array(
					'onchange'=>"$.fn.yiiGridView.update('list-grid',{data:{'col':'add_time','dt': $(this).val()}})", 
					'style'=>'width: 100px',
				)
		  )
		  	,'headerHtmlOptions'=>array('style'=>'width: 100px')
		  	,'htmlOptions'=>array('style'=>'width: 100px')
			,'value'=>'Tak::timetodate($data->add_time,6)'
		),
		array(
			'name'=>'status'
			,'type'=>'raw'
			,'sortable'=>false
		  	,'headerHtmlOptions'=>array('style'=>'width: 100px')
			,'value'=>'$data->getState()'
			,'htmlOptions'=>array('class'=>'badge red pagination-centere')
		)
	);

	$widget = $this->widget('bootstrap.widgets.TbGridView', $listOptions);
?>
		</div>
	</div>
</div>
