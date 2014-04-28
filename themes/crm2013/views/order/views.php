<?php
/* @var $this OrderController */
/* @var $model Order */
/* @var $form bootstrap.widgets.TbActiveForm */
?>
<?php
$itemid = $model->itemid;
$fromid = $model->fromid;
$this->breadcrumbs = array(
    Tk::g($model->sName) => array(
        'admin'
    ) ,
    $itemid,
);
$orderInfo = $model->getOrderInfo();
$listStatus = $model->getListStatus();

$pay_type = OrderConfig::getAlipay($orderInfo->pay_type, $fromid);
?>


<div class="tak-order-status">
	<?php echo CHtml::image($this->getAssetsUrl() . 'img/tak/' . $model->status . '.png') ?>
</div>

<div class="well">
<strong><?php echo $model->getAttributeLabel('itemid'); ?></strong>：
	<?php echo $model->itemid; ?>
	，
<strong><?php echo $model->getAttributeLabel('status'); ?></strong>：
	<?php echo OrderType::item('order-status', $model->status); ?>
<p>
	<?php echo $model->getAttributeLabel('add_time'); ?>：
	<?php echo Tak::timetodate($model->add_time, 6); ?>
	，
	<?php echo $model->getAttributeLabel('total'); ?>：
	<strong class="price-strong">
		<?php echo Tak::format_price($model->total); ?>
	</strong>
	，
	<?php echo $model->getAttributeLabel('manageid'); ?>：
	<?php
if (isset($model->iManage)) {
    echo CHtml::link($model->iManage->company, Yii::app()->createUrl('/Site/PreviewTestMember', array(
        'id' => $model->manageid
    )) , array(
        'class' => 'data-ajax',
        'title' => $model->iManage->company
    ));
} else {
    echo '未知';
}
?>
	<!--，
	<?php echo $model->getAttributeLabel('add_ip'); ?>：
	<?php echo Tak::Num2IP($model->add_ip); ?>
	-->
</p>
<p>
	<?php
if ($model->pay_time > 0) {
    echo $model->getAttributeLabel('pay_time') . '：' . Tak::timetodate($model->pay_time, 6) . '，';
}
if ($model->delivery_time > 0) {
    echo $model->getAttributeLabel('delivery_time') . '：' . Tak::timetodate($model->delivery_time, 6);
}
?>
</p> 
<?php
$this->renderPartial('_info', array(
    'model' => $model,
    'orderInfo' => $orderInfo,
    'pay_type' => $pay_type,
));
?>