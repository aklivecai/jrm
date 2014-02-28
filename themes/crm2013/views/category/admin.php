<?php
$this->breadcrumbs=array(
	Tk::g($this->getType().' Category')=>$this->cateUrl,
	Tk::g('Admin'),
);

$items = array(  
    array(
      'icon' =>'isw-plus',
      'url' => $this->getLink('Create'),
      'label'=>Tk::g('Create'),
    )    
);

?>
   <div class="row-fluid">
                        <div class="head clearfix">
                            <div class="isw-list"></div>
                            <h1><?php echo Tk::g('List'); ?></h1>
<?php 
$this->widget('application.components.MyMenu',array(
      'htmlOptions'=>array('class'=>'buttons'),
      'items'=> $items ,
));
?>                                      
                        </div>
                        <div class="block-fluid clearfix">
					<?php 
					$widget = $this->widget('bootstrap.widgets.TbGridView', array(
							    'type'=>'striped bordered condensed',
							    'id' => 'list-grid',
								'dataProvider'=>$model->search(),
								'template' => '{summary}<div class="dr"><span></span></div>{items}',
							    	'summaryCssClass' => 'dataTables_info',
							    	'pagerCssClass' => 'pagination dataTables_paginate',
								'columns'=>array(
									array(
										'name'=>'listorder',
										'type'=>'raw',
										'headerHtmlOptions'=>array('style'=>'width: 85px'),
									),
									array(
										'name'=>'catename',
										'type'=>'raw',
									),
									array(
										'name'=>'item',
										'type'=>'raw',
									),
									array(
										 'class'=>'bootstrap.widgets.TbButtonColumn'
										  ,'htmlOptions'=>array('style'=>'width: 85px')
										  ,'template' => '{update} {delete}'
										  , 'deleteButtonUrl' => '$data->getDelLink()'
										  , 'updateButtonUrl' => '$data->getEidtLink()'

									),			
								),
							)); 
							?>
				</div>

            
			<div class="dr"><span></span></div>
		</div>
	</div>