<?php
/**
 * Google QR Code make
 * @Author: Rogee<rogeecn@gmail.com>
 * Date: 12-12-17 下午1:36
 * Version 1.0.0
 */

class GoogleQRCode extends CWidget
{
    public $htmlOptions;
    private $_htmlOptions = array(
        'title' => 'qrcode',
        'alt' => 'arcode',
    );
    public $size = 150;
    public $content = 'Hello World';
    public $level = 'L';
    public $margin = '1';

    private $_width;
    private $_height;
    public function init(){
        $this->size = intval($this->size);
        $_content = $this->content;
        if (!Tak::isUrl($_content)) {
            $_content = urlencode($_content);
        }
        $data = array(
            'cht' => 'qr',
            'chs' => $this->size.'x'.$this->size,
            'chl' => $_content,
            'chld' => $this->level.'|'.$this->margin,
        );

        $src = sprintf("https://chart.googleapis.com/chart?%s", http_build_query($data));
        // $src = sprintf("https://127.0.0.1/chart?%s", http_build_query($data));
        $_htmlOptions = array(
            'src' => $src,
            'width' => $this->size,
            'height' => $this->size,
        );

        $this->htmlOptions = array_merge($this->_htmlOptions, (array)$this->htmlOptions,  $_htmlOptions);
    }

    public function run(){
        echo CHtml::tag('img', $this->htmlOptions);
    }
}