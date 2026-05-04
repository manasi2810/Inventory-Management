<?php

namespace App\Exports;

use App\Services\ReportService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StockLedgerExport
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function download($fileName = 'stock_ledger.xlsx')
    {
        $service = app(ReportService::class);
        $data = $service->getStockLedgerReport($this->filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', '#');
        $sheet->setCellValue('B1', 'Date');
        $sheet->setCellValue('C1', 'Product');
        $sheet->setCellValue('D1', 'Movement');
        $sheet->setCellValue('E1', 'Qty');
        $sheet->setCellValue('F1', 'Reference Type');
        $sheet->setCellValue('G1', 'Reference ID');
        $sheet->setCellValue('H1', 'Balance');

        $row = 2;
        $i = 1;

        foreach ($data as $item) {
            $sheet->setCellValue('A'.$row, $i++);
            $sheet->setCellValue('B'.$row, date('d-m-Y', strtotime($item->created_at)));
            $sheet->setCellValue('C'.$row, $item->product->name ?? '-');
            $sheet->setCellValue('D'.$row, $item->movement_type);
            $sheet->setCellValue('E'.$row, $item->qty);
            $sheet->setCellValue('F'.$row, $item->reference_type);
            $sheet->setCellValue('G'.$row, $item->reference_id);
            $sheet->setCellValue('H'.$row, $item->balance_after);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }
}