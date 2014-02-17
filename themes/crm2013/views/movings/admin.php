<?php
/* @var $this MovingsController */
/* @var $model Movings */

$this->breadcrumbs=array(
	Tk::g($model->sName) => array('admin'),
	Tk::g('Admin'),
);

$subItems = array('label'=>Tk::g('Entering'), 'url'=>$this->createUrl('create'),'icon'=>'plus');
$_subItems = array();
foreach ($this->cates as $key => $value) {
	$_subItems[] = array('label'=>$value, 'url'=>$this->createUrl('create',array('Movings[typeid]'=>$key)),'icon'=>'isw-text_document');
}
      $listMenu = array(  
            'Create'=>array(
              'icon' =>'isw-plus',
              'url' => array('create'),
              'label'=>Tk::g('Entering'),
              'items' => $_subItems,
              'submenuOptions' => array('class'=>'dd-list'),
            )
       );

?>
<div class="row-fluid">
	<div class="span12">
	<div class="head clearfix">
        <div class="isw-grid"></div>
        <h1><?php echo Tk::g($model->sName) ?></h1>   
		<?php 
		$this->widget('application.components.MyMenu',array(
		      'htmlOptions'=>array('class'=>'buttons'),
		      'items'=> $listMenu ,
		));
		?>    
	</div>	
	<div class="block-fluid clearfix">
<?php 
		$this->renderPartial('//_search',array('model'=>$model)); 
		$this->renderPartial("/movings/_search",array('model'=>$model,'cates'=>$this->cates));

// $cates = $this->cates;
$options = Tak::gredViewOptions(false);
$options['dataProvider'] = $model->search();
$columns = array(	
		Tak::getAdminPageCol()
		,array(
			'name'=>'typeid',
			'type'=>'raw',
			'value'=>'TakType::getStatus("'.$this->typename.'-type",$data->typeid)',
			'filter'=>$cates, 
			'headerHtmlOptions'=>array('style'=>'width:100px;'),
			'header'=> $model->getAttributeLabel("typeid"),
		)
		,array(
			'name'=>'enterprise',
			'type'=>'raw',
            		'sortable' => false,
            		'header'=> $model->getAttributeLabel("enterprise"),
		)	
,		'numbers'

		,array(
			'name'=>'us_launch',
			'type'=>'raw',
            		'sortable' => false,
		)	
		,array(
			'name'=>'time',
			'type'=>'raw',
			'value'=>'Tak::timetodate($data->time)',
            		'sortable' => false,
            		'headerHtmlOptions'=>array('class'=>'stor-date'),
            		'header'=> $model->getAttributeLabel("time"),
		)	
		,array(
			'name'=>'time_stocked',
			'type'=>'raw',
			'value'=>'TakType::getStatus("isok",$data->time_stocked>0?1:0)',
            		'sortable' => true,
            		'headerHtmlOptions'=>array('class'=>'stor-date'),
	            'header'=> Tk::g($model->sName),
		)	
	);
	$options['columns'] = $columns;
	$widget = $this->widget('bootstrap.widgets.TbGridView', $options); 		

?>
		</div>
	</div>
</div>
