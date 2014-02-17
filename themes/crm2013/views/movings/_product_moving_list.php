<?php
/* @var $this MovingsController */
/* @var $data Movings */
?>
<tr>
	<td><?php echo $data->iMovings->numbers; ?></td>
	<td><?php echo CHtml::encode($data->iMovings->enterprise); ?></td>
	<td><?php echo $cates[$data->iMovings->typeid]; ?></td>
	<td class="txt-right"><?php echo CHtml::encode($data->numbers); ?></td>
	<td class="txt-center"><?php echo Tak::timetodate($data->iMovings->time,3); ?></td>
	<td class="txt-center"><?php echo CHtml::encode($data->iMovings->us_launch); ?></td>
	<td class="txt-center"><?php echo Tak::timetodate($data->time_stocked,6); ?></td>
</tr>