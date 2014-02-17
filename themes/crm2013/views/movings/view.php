<?php
/* @var $this MovingsController */
/* @var $model Movings */

$this->breadcrumbs=array(
	Tk::g($model->sName) => array('admin'),
	$model->itemid,
);

    $items = Tak::getViewMenu($model->itemid);
    $items['Create']['label'] = Tk::g('Entering');

	$tags = array( 
        'numbers',
        'time' => array('name'=>'time', 'value'=>Tak::timetodate($model->time)),
        'enterprise',
        'us_launch',
        'note',
        'time_stocked' => array('name'=>'time_stocked', 'value'=>Tak::timetodate($model->time_stocked,6)),
        'add_time' => array('name'=>'add_time', 'value'=>Tak::timetodate($model->add_time,6),),
        'modified_time' => array('name'=>'modified_time', 'value'=>Tak::timetodate($model->modified_time,6),),
	);

$nps = $model->getNP(true);
if (count($nps)>0) {
    $_itemis[] = 
        array('label'=>Tk::g(array('More',$model->sName)), 'url'=>'#', 'icon'=>'list','itemOptions'=>array('data-geturl'=>$this->createUrl('gettop',array('id'=>$model->primaryKey)),'class'=>'more-list'),'submenuOptions'=>array('class'=>'more-load-info'),'items'=>array(
            array('label'=>'...', 'url'=>'#'),
        )
    );
   array_splice($_itemis,count($_itemis),0,Tak::getNP($nps));
}
 array_splice($items,count($items)-2,0,$_itemis);    


if ($model->time_stocked>0) {
        unset($items['Update']);
        unset($items['Delete']);

        $items[] = array('label'=>Tk::g('Print'), 'icon'=>'print','url'=>array('print', 'id'=>$model->itemid),'linkOptions'=>array('target'=>'_blank'));
	}else{
        unset($tags['time_stocked']);
        if (ProductMoving::model()->recentlyByMovingid($model->itemid)->count()>0) {
            # code...
        $items['affirm'] = array(
            'label'=>Tk::g(array('Affirm',$model->sName)),
             'icon'=>'ok-sign',
             'url'=>array('affirm', 'id'=>$model->itemid),
             'linkOptions'=>array('id'=>'btn-affirm')
             );
        }
    }
	

?>
<div class="block-fluid without-head">
	<div class="row-fluid ">
<div class="toolbar nopadding-toolbar clear clearfix">
	<h4><?php echo Tk::g(array($model->getTypeName(),'bill')); ?> (<?php echo $this->cates[$model->typeid] ?>)</h4>  
</div>	
<div class="dr"><span></span></div>
<div class="span3">
    <?php
        $str = '<ul class="rows">
    			<li class="heading">单据信息</li>';
    	foreach ($tags as $key => $value) {
    		if (is_numeric($key)) {
    			  $key = $value;  
    			  $value = CHtml::encode($model->{$value});
    		}else{
    			 $value = $value['value'];
    		}	
    		// Tak::KD($tags,1);
			    $str.='<li><div class="title">'.
			    	CHtml::encode($model->getAttributeLabel($key))
			    .":</div><div class=\"text\">&nbsp;$value </div></li>";
    	}
    	$str.= '</ul>';
    	echo $str;
    ?>     

</div>
<div class="span6">
      <div class="block-fluid without-head">
        <div class="toolbar nopadding-toolbar clearfix">
          <h4>产品明细</h4>
        </div>
 <?php $this->widget('bootstrap.widgets.TbListView', array(
			'dataProvider' => $model->getProductMovings(),
			'itemView'=>'//movings/_product_view',
			'template'=>'<table class="table"> <thead> <tr> <th>物料名字</th> <th>产品规格</th> <th>材料</th> <th>单位</th>  <th>颜色</th><th>价格</th><th>数量</th> </tr> </thead> <tbody>{items}</tbody> </table>',
			'htmlOptions'=>array('class'=>''),
            'emptyText'=>'<tr><td colspan="6">没有数据!</td></tr>'
		)); ?> 
		</div>
</div>
<div class="span2">
<?php $this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'list',
    'items'=> $items,
    )
); 
?>
</div>
<div class="dr"><span></span></div>
</div>
</div>