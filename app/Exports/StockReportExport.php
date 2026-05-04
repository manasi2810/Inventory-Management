<?php

namespace App\Exports;

use App\Services\ReportService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StockReportExport
{
    protected $service;

    public function __construct($service)
    {
        $this->service = $service;
    }

    public function download($fileName = 'stock_report.xlsx')
    {
        $data = $this->service->getStockReport();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // HEADER
        $sheet->setCellValue('A1', '#');
        $sheet->setCellValue('B1', 'Product');
        $sheet->setCellValue('C1', 'Opening Stock');
        $sheet->setCellValue('D1', 'Stock In');
        $sheet->setCellValue('E1', 'Stock Out');
        $sheet->setCellValue('F1', 'Available Stock');

        $row = 2;
        $i = 1;

        foreach ($data as $item) {
            $sheet->setCellValue('A'.$row, $i++);
            $sheet->setCellValue('B'.$row, $item->name);
            $sheet->setCellValue('C'.$row, $item->opening_stock ?? 0);
            $sheet->setCellValue('D'.$row, $item->total_in ?? 0);
            $sheet->setCellValue('E'.$row, $item->total_out ?? 0);
            $sheet->setCellValue('F'.$row, $item->available_stock ?? 0);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }
}