<?php
/**
 *
 * @authors aklivecai (aklivecai@gmail.com)
 * @date    2014-04-24 10:02:53
 * @version $Id$
 */
class HtmlEdit extends CInputWidget {
    const COLS = 40;
    const ROWS = 10;
    
    private $options = array();
    public $width = '100%';
    public $height = '400px';    
    public  $theme = 'default';    
    public  $toolbar = array();

    public $htmlOptions = array();

    
    public function __construct($owner = null) {
        parent::__construct($owner);
    }
    public function setToolbar($value) {
        if (is_array($value) || is_string($value)) {
            $this->toolbar = $value;
        } else throw new CException(Yii::t(__CLASS__, 'toolbar must be an array or string'));
    }
    public function getToolbar() {
        return $this->toolbar;
    }
    public function setOptions($value) {
        if (!is_array($value)) throw new CException(Yii::t(__CLASS__, 'options must be an array'));
        $this->options = $value;
    }
    
    public function getOptions() {
        return $this->options;
    }
    protected function makeOptions() {
        list($name, $id) = $this->resolveNameID();
        $options['toolbar'] = $this->toolbar;
        // here any option is overriden by user's options
        if (is_array($this->options)) {
            $options = array_merge($options, $this->options);
        }
        return CJavaScript::encode($options);
    }
    public function run() {
        parent::run();
        list($name, $id) = $this->resolveNameID();
        
        $options = $this->makeOptions();
        
        $cs = Yii::app()->getClientScript();
        $cs->registerScriptFile(Yii::app()->params['staticUrl'] . '_ak/ckeditor/ckeditor.js');
        $this->htmlOptions['id'] = $id;
        if (!array_key_exists('style', $this->htmlOptions)) {
            $this->htmlOptions['style'] = "width:{$this->width};height:{$this->height};";
        }
        if (!array_key_exists('cols', $this->htmlOptions)) {
            $this->htmlOptions['cols'] = self::COLS;
        }
        if (!array_key_exists('rows', $this->htmlOptions)) {
            $this->htmlOptions['rows'] = self::ROWS;
        }
        
        $js = <<<EOP
CKEDITOR.replace('{$name}',{$options});
EOP;
        $cs->registerScript('Yii.' . get_class($this) . '#' . $id, $js, CClientScript::POS_END);
        
        if ($this->hasModel()) {
            $html = CHtml::activeTextArea($this->model, $this->attribute, $this->htmlOptions);
        } else {
            $html = CHtml::textArea($name, $this->value, $this->htmlOptions);
        }
        
        echo $html;
    }
}
