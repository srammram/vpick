<?php defined('BASEPATH') OR exit('No direct script access allowed');

if(! function_exists('create_excel')) {
    function create_excel($excel, $filename) {
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		ob_end_clean();
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        exit;
    }
}
