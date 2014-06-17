<?php
class Xls {
    private $xls = null;
    private $dir = null;
    public function getXls() {
        if ($this->xls == null) {
            $this->dir = Tak::getUserDir() . 'toxls/';
            // Tak::KD($this->dir,1);
            Tak::MkDirs($this->dir);
            spl_autoload_unregister(array(
                'YiiBase',
                'autoload'
            ));
            $phpExcelPath = Yii::getPathOfAlias('application.extensions.phpexcel');
            
            include ($phpExcelPath . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            spl_autoload_register(array(
                'YiiBase',
                'autoload'
            ));
            
            $this->xls = new PHPExcel();
            // echo 12;
            $this->xls->getProperties()->setCreator("Maarten Balliauw")->setLastModifiedBy("Maarten Balliauw")->setTitle("Office 2007 XLSX Test Document")->setSubject("Office 2007 XLSX Test Document")->setDescription("Document for Office 2007 XLSX, generated using PHP classes.")->setKeywords("office 2007 openxml php")->setCategory("Test result file");
        }
        return $this->xls;
    }
    /**
     *  输出数据xls,title,header,data,footer,name=>输出的名字
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function toXLs($data) {        
        $objPHPExcel = $this->getXls();
        $headList = $data['headers'];
        $headerText = $data['headerText'];
        $list = $data['datas'];
        $fileurl = $this->dir;
        $tname = '';
        if (isset($data['file'])) {
            $tname.= $data['file'];
        }
        $tname.= date('YmdHis');
        $fileurl.= $tname . '.xls';
        $ki = 0;
        $col = 3;
        $getActiveSheet  = $objPHPExcel->getActiveSheet();
        foreach ($headList as $k => $v) {
            $ki++;
            $newkey = chr(64 + $ki);
            $headList[$k]['key'] = $newkey;
            //宽度
            $getActiveSheet->getColumnDimension($newkey)->setWidth($v['width']);
            // 行高
            $getActiveSheet->getRowDimension($ki + 2)->setRowHeight(22);
            // 加粗
            $getActiveSheet->getStyle($newkey . $col)->getFont()->setBold(true);
            $getActiveSheet->setCellValue($newkey . $col, $v['name']);
        }
        /*合并单元格*/
        $getActiveSheet->mergeCells('A1:' . $newkey . '1');
        $getActiveSheet->mergeCells('A2:' . $newkey . '2');
        
        $objRichText = new PHPExcel_RichText();
        $objRichText->createText('');
        $objPayable = $objRichText->createTextRun($title);
        $objPayable->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));
        $objPayable->getFont()->setBold(true);
        $objPayable->getFont()->setSize(24);
        
        $getActiveSheet->getCell('A1')->setValue($objRichText);
        $getActiveSheet->getStyle('A1')->getFont()->setBold(true); // 加粗
        $getActiveSheet->getStyle('A1')->getFont()->setSize(24); // 字体大小
        $getActiveSheet->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED); // 文本颜色
        
        $getActiveSheet->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        // 底纹
        $getActiveSheet->getStyle('A1')->getFill()->getStartColor()->setARGB('00FFFFE3');
        $cols = 4;

        foreach ($list as $ks => $vs) {
            foreach ($headList as $k => $v) {
                $tstr = $vs[$k];
                $getActiveSheet->setCellValue($v['key'] . ($ks + $cols) , trim($tstr));
            }
        }
        
        $getActiveSheet->setCellValue('A2', $headerText);
        $objPHPExcel->setActiveSheetIndex(0);
        $getActiveSheet->setTitle($tname);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        
        // Tak::KD($fileurl);
        $objWriter->save($fileurl);
        return $fileurl;
    }
}
