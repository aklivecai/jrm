<?php $this->breadcrumbs = array(
    Rights::t('core', 'Rights').''=>Rights::getBaseUrl(),
	Rights::t('core', 'Tasks'),
); ?>

<div class="row-fluid" id="tasks">
<div class="head clearfix">
    <i class="isw-documents"></i> <h1><?php echo Rights::t('core', 'Tasks');?></h1>
<ul class="buttons">
    <li>
        <?php echo CHtml::link('', array('authItem/create', 'type'=>CAuthItem::TYPE_TASK), array(
        'class'=>'isw-plus',
         'title'=>Rights::t('core', 'Create a new task')
    )); ?>
    </li>
</ul>       
</div>
<div class="block-fluid clearfix">
	<?php  $widget = $this->widget('bootstrap.widgets.TbGridView', array(
	    'dataProvider'=>$dataProvider,
	    'template'=>'{items}',
	    'emptyText'=>Rights::t('core', 'No tasks found.'),
	    'htmlOptions'=>array('class'=>'grid-view task-table'),
	    'columns'=>array(
    		array(
    			'name'=>'name',
    			'header'=>Rights::t('core', 'Name'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'name-column'),
                                        // 'value'=>'$data->getGridNameLink()',
    			 'value'=>'CHtml::link($data->name,array("update","name"=>Tak::encrypt($data->name)))',

    		),
    		array(
    			'name'=>'description',
    			'header'=>Rights::t('core', 'Description'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'description-column'),
    		),
    		array(
    			'name'=>'bizRule',
    			'header'=>Rights::t('core', 'Business rule'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'bizrule-column'),
    			'visible'=>Rights::module()->enableBizRule===true,
    		),
    		array(
    			'name'=>'data',
    			'header'=>Rights::t('core', 'Data'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'data-column'),
    			'visible'=>Rights::module()->enableBizRuleData===true,
    		),
    		array(
    			'header'=>'&nbsp;',
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'actions-column'),
    			'value'=>'$data->getDeleteTaskLink()',
    		),
	    )
	)); ?>

	<p class="info"><?php echo Rights::t('core', 'Values within square brackets tell how many children each item has.'); ?></p>
</div>
</div>