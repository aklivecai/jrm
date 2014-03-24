<?php
$this->pageTitle = Yii::app()->name . Tk::g('Database back');
$this->breadcrumbs = array(
    Tk::g('Database back') ,
);

Tak::KD(Yii::app()->db->schema->getTableNames());

$random = Tak::timetodate(Tak::now(), 'Y-m-d H.i.s').' '.strtolower(Tak::createCode(10));

echo $random;

function sql_dumptable($table, $startfrom = 0, $currsize = 0,$sizelimit=2048) {
    if(!isset($tabledump)) $tabledump = '';
    $offset = 100;
    $tabledumped = 0;
    $numrows = $offset;
    while($currsize + strlen($tabledump) < $sizelimit * 1000 && $numrows == $offset) {
        $tabledumped = 1;
        $rows = $db->query("SELECT * FROM `$table` WHERE 1=1 $condition  LIMIT $startfrom, $offset");
        $numfields = $db->num_fields($rows);
        $numrows = $db->num_rows($rows);
        while($row = $db->fetch_row($rows)) {
            $comma = "";
            $tabledump .= "INSERT INTO $table VALUES(";
            for($i = 0; $i < $numfields; $i++) {
                $tabledump .= $comma."'".mysql_escape_string($row[$i])."'";
                $comma = ",";
            }
            $tabledump .= ");\n";
        }
        $startfrom += $offset;
    }
    $startrow = $startfrom;
    $tabledump .= "\n";
    return $tabledump;
}
?>


<div class="row-fluid">
	<div class="span12">
	<div class="head clearfix">
        <div class="isw-grid"></div>
        <h1><?php echo Tk::g('Database back') ?></h1>
	</div>
	<div class="block-fluid clearfix">
	<div class="dr "><span></span></div>
    <div class="span1">
        备份进度
    </div>                            
    <div class="span10">
    <?php
$this->widget('bootstrap.widgets.TbProgress', array(
    'type' => 'success', // 'info', 'success' or 'danger'
    'percent' => 1, // the progress
    'striped' => true,
    'animated' => true,
    'htmlOptions' => array(
        'id' => 'tak_percent'
    )
));
?>    
    </div>
    <div class="dr clearfix"><span></span></div>
    <button type="button" class="btn">备份数据</button>
    <div class="dr clearfix"><span></span></div>
</div>
</div>
</div>
<script>
(function($){
	var bar = $('#tak_percent .bar')
		, iwidth = (100 * parseFloat(bar.css('width'))/parseFloat(bar.parent().width()))
	; 
	window.p = function(){
			iwidth+=0.1;
			if (iwidth>=100) {
				window.clearInterval(int);
			};
			bar.css('width',iwidth+'%')			
		}
	;
	 var init = setInterval('p()',1000);
})(jQuery);

</script>