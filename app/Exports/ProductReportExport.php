<?php

namespace App\Exports;

use App\Services\ReportService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProductReportExport
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function download($fileName = 'product_report.xlsx')
    {
        $service = app(ReportService::class);
        $products = $service->getProductReport($this->filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'SR');
        $sheet->setCellValue('B1', 'Product');
        $sheet->setCellValue('C1', 'SKU');
        $sheet->setCellValue('D1', 'Opening Stock');
        $sheet->setCellValue('E1', 'Stock In');
        $sheet->setCellValue('F1', 'Stock Out');
        $sheet->setCellValue('G1', 'Available Stock');

        $row = 2;
        $i = 1;

        foreach ($products as $p) {
            $sheet->setCellValue('A'.$row, $i++);
            $sheet->setCellValue('B'.$row, $p->name);
            $sheet->setCellValue('C'.$row, $p->sku ?? '-');
            $sheet->setCellValue('D'.$row, $p->opening_stock);
            $sheet->setCellValue('E'.$row, $p->total_in);
            $sheet->setCellValue('F'.$row, $p->total_out);
            $sheet->setCellValue('G'.$row, $p->available_stock);

            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }
}