<?php
/* @var $this StocksController */
/* @var $model Stocks */

$this->breadcrumbs=array(
	Tk::g($this->modelName) => array('index'),
);
$items = Tak::getListMenu();
?>
<div class="row-fluid">
	<div class="span12">
	<div class="head clearfix">
        <div class="isw-grid"></div>
        <h1><?php echo Tk::g(array($this->modelName))?></h1>
	</div>	

	<div class="block-fluid clearfix">

<?php $this->renderPartial('/product/_search',array('model'=>$model)); ?>

<?php 

$options = Tak::gredViewOptions(false);
$tags = $model->search();
if ($tags->itemCount>0) {
	$crite = $tags->getCriteria();
	$sql = $crite->condition;
	if ($sql) {
		$t = $crite->params;
		foreach ($t as $key => $value) {
			$t[$key] = "'$value'";
		}
		$sql = strtr($sql, $t);
	}
	$totals = Product::getTotals($sql);
	$str .= '<div class="tar"><i class="icon-tasks"></i>总价格: <span class="badge badge-important">:ptotal</span>   <i class="icon-tasks"></i>总数量: <span class="badge badge-warning">:stotal</span>     </div><div class="dr"><span></span></div>';
	$str = strtr($str,array(
		':stotal'=>$totals['stotal'],
		':ptotal'=>Tak::format_price($totals['ptotal']),
	));
	$options['template'] = $options['template'].$str;
}
$columns = array(	
		array(
			'name'=>'name',
			'type'=>'raw',
			'value'=>'CHtml::link($data->name,array("viewProduct","id"=>$data->primaryKey))',
		)	
,		array(
			'name'=>'material',
			'value'=>'$data->material',
		)
,		array(
			'name'=>'spec',
			'value'=>'$data->spec',
		)		
,		array(
			'name'=>'color',
			'value'=>'$data->color',
		)				
,		array(
			'name'=>'数量',
			'type'=>'raw',			
			'value'=>'Tak::tagNum($data->stock)',
		)				
,		array(
			'name'=>'price',
			'value'=>'Tak::format_price($data->price)',
		)
,		array(
			'name'=>'小计',
			'value'=>'Tak::format_price($data->total)',
		)
	);
	
	$options['columns'] = $columns;	
	$options['dataProvider'] = $tags;
	$widget = $this->widget('bootstrap.widgets.TbGridView', $options); 
?>
		</div>
	</div>
</div>
