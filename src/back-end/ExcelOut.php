<?php
require_once  __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/data.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelOut {
    public static function genel_uye_paroru() {
        $spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();


$data = new Data();
$list = $data->genel_uye_paroru_xls();


if (is_array($list[0])) {
    $fields = array_keys($list[0]);
    for($i=0; $i<count($fields); $i++) {
        $cell = chr(65+$i)."1";
        $field = $fields[$i];
        $sheet->setCellValue($cell, $field);
    }

    for ($rind=0; $rind<count($list); $rind++) {
        for($cind = 0; $cind < count($fields); $cind++ ) {
            $value = $list[$rind][$fields[$cind]];
            $cell = chr(65+$cind).($rind+2);
            if ( in_array($fields[$cind],["dogum_tarihi","ilk_keiko","son_keiko"]) ) {
                $dv = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($value);
                $sheet->setCellValue($cell, $dv);
                $sheet->getStyle($cell)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
            } else {
                $sheet->setCellValue($cell, $value);
            }
            
        }
    }

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="genel_uye_raporu.xlsx"');
    $writer->save('php://output');
}

    }
}