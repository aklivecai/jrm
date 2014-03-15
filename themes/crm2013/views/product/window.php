<?php
/* @var $this ProductController */
/* @var $model Product */
$this->breadcrumbs=array(
	Tk::g($model->sName) => array('admin'),
	Tk::g('Admin'),
);
$items = Tak::getListMenu();
?>

<div class="row-fluid">
	<div class="block-fluid clearfix">
<?php 
$tags = $model->search();
$m = $tags->getData();
echo Tak::modulesToJson($m);

$this->renderPartial("_search",array('model'=>$model,'notcate'=>true,'warehouse'=>true));
$this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'link', 'label'=>'选择产品','htmlOptions'=>array("id"=>"tak-select"))); 
$options = Tak::gredViewOptions(false);
$options["afterAjaxUpdate"]=null;
$options['dataProvider'] = $tags;
$columns = array(	
                    array(
                            'class'=>'CCheckBoxColumn',
                            'name'=>'itemid',
                            'selectableRows'=>1,
                            'headerTemplate'=>'<input type="checkbox" id="select_all">',
                            'selectableRows' => 2,
                            'headerHtmlOptions' => array('width'=>'25px','align'=>'center'),
                            'checkBoxHtmlOptions' => array(
                                'name' => 'itemid[]',
                                'align'=>'center',
                                'class'=>'check',
                            ),        

                    ),
		'name'
		,array(
			'name'=>'typeid',
			'type'=>'raw',
			'value'=>'Category::getProductName($data->typeid)',
		)
		,array(
			'name'=>'material',
			'type'=>'raw',
		)
		,array(
			'name'=>'spec',
			'type'=>'raw',
		)
		,array(
			'name'=>'color',
			'type'=>'raw',
		)
		,array(
			'name'=>'price',
			'type'=>'raw',
			'htmlOptions'=>array('style'=>'width:120px;'),
		)	
	);
	$options['columns'] = $columns;
	$widget = $this->widget('bootstrap.widgets.TbGridView', $options); 
?>
		</div>
	</div>
</div>
<?php
Tak::regScript('end','
$(document).on("click","#select_all",function(){
 	var t = $(this).prop("checked");
 	$("#list-grid .check").each(function(i,el){
		$(el).prop("checked",t);
 	});
 }).on("click","#tak-select",function(){
 	var list = $("#list-grid .check:checked")
 	, length = list.length
 	;
 	if (length==0) {
 		alert("未有选中产品!");
 		return false;
 	}
 	var data = [];
 	for (i=0; i <length ; i++) { 
 		data.push(list.eq(i).val());
 	}
 })

var select = function(data){
	var list = "";
}
');
?>