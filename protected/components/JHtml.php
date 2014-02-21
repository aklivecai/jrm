<?php  

class JHtml extends CHtml{
    public static function tag($tag,$content=false,$htmlOptions=array(),$closeTag=true){
        $html = parent::tag($tag,$htmlOptions,$content,$closeTag);
        return $html;
    }
}