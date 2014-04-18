<?php
/* @var $this OrderController */
/* @var $model Order */

$this->breadcrumbs = array(
    Tk::g('Order') => array(
        'admin'
    ) ,
    Tk::g('Admin') ,
);
$items = Tak::getListMenu();
?>
<div class="row-fluid">
    <div class="span12">
        <div class="head clearfix">
            <div class="isw-grid"></div>
            <h1><?php echo Tk::g('Order') ?></h1>
        </div>

<div class="block-fluid clearfix">
            <?php $this->renderPartial('_search', array(
    'model' => $model
));

$listOptions = Tak::gredViewOptions(false);
$listOptions['dataProvider'] = $model->search();
$listOptions['columns'] = array(
    array(
        'class' => 'bootstrap.widgets.TbButtonColumn',
        'template' => ' <div style="font-weight: bold;text-align: center;line-height:55px;">{updates}</div> '
        /*<br />{addproduct} <br />{addorderinfo} <br />{addorderflow}*/
        ,
        'buttons' => array(
            'updates' => array(
                'label' => '马上处理',
                'url' => '$data->getLinkUP()',
                'options' => array(
                    'style' => 'color: red;'
                )
            ) ,
            'addproduct' => array(
                'label' => '添加商品',
                'url' => 'Yii::app()->createUrl("OrderProduct/create", array("OrderProduct[order_id]"=>$data->primaryKey,"OrderProduct[fromid]"=>$data->fromid))',
            ) ,
            'addorderinfo' => array(
                'label' => '添加订单详情',
                'url' => 'Yii::app()->createUrl("OrderInfo/create", array("OrderInfo[itemid]"=>$data->primaryKey))',
            ) ,
            'addorderflow' => array(
                'label' => '添加流程',
                'url' => 'Yii::app()->createUrl("OrderFlow/create", array("OrderFlow[order_id]"=>$data->primaryKey))',
            ) ,
        ) ,
        'header' => CHtml::dropDownList('pageSize', Yii::app()->user->getState('pageSize') , TakType::items('pageSize') , array(
            'onchange' => "$.fn.yiiGridView.update('list-grid',{data:{setPageSize: $(this).val()}})",
        )) ,
        'htmlOptions' => array(
            'style' => 'width: 85px'
        )
    ) ,
    array(
        'name' => 'itemid',
        'headerHtmlOptions' => array(
            'style' => 'width: 120px'
        )
    ) ,
    array(
        'name' => '订单产品',
        'type' => 'raw',
        'value' => '$data->wProducts()'
    ) ,
    array(
        'name' => '预期交货日期',
        'type' => 'raw',
        'headerHtmlOptions' => array(
            'style' => 'width: 85px'
        ) ,
        'value' => 'Tak::timetodate($data->iOrderInfo->date_time,3)'
    ) ,
    array(
        'name' => 'total',
        'headerHtmlOptions' => array(
            'style' => 'width: 85px'
        ) ,
        'value' => 'Tak::format_price($data->total)',
        'htmlOptions' => array(
            'class' => 'price-strong'
        )
    ) ,
    array(
        'name' => 'add_time',
        'type' => 'raw',
        'headerHtmlOptions' => array(
            'style' => 'width: 100px'
        ) ,
        'htmlOptions' => array(
            'style' => 'width: 100px'
        ) ,
        'value' => 'Tak::timetodate($data->add_time,6)'
    ) ,
    array(
        'name' => 'status',
        'type' => 'raw',
        'sortable' => false,
        'headerHtmlOptions' => array(
            'style' => 'width: 100px'
        ) ,
        'value' => '$data->getState()',
        'htmlOptions' => array(
            'class' => 'red '
        )
    )
);

$widget = $this->widget('bootstrap.widgets.TbGridView', $listOptions);
?>
        </div>
    </div>
</div>