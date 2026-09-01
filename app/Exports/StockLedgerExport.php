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

        $data = $service
            ->getStockLedgerReport($this->filters);

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        | getStockLedgerReport() currently returns paginate(20).
        | For Excel we should export ALL matching records,
        | not only the current 20 records.
        |--------------------------------------------------------------------------
        */

        $data = $data->getCollection();

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        // =====================================================
        // HEADER
        // =====================================================

        $headers = [
            'A1' => '#',
            'B1' => 'Date & Time',
            'C1' => 'Product ID',
            'D1' => 'Product',
            'E1' => 'SKU',
            'F1' => 'Movement Type',
            'G1' => 'Quantity',
            'H1' => 'Balance After',
            'I1' => 'Reference Type',
            'J1' => 'Reference ID',
            'K1' => 'Created By',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // =====================================================
        // DATA
        // =====================================================

        $row = 2;
        $i = 1;

        foreach ($data as $item) {

            $sheet->setCellValue(
                'A' . $row,
                $i++
            );

            $sheet->setCellValue(
                'B' . $row,
                $item->created_at
                    ? $item->created_at->format('d-m-Y h:i A')
                    : '-'
            );

            $sheet->setCellValue(
                'C' . $row,
                $item->product_id
            );

            $sheet->setCellValue(
                'D' . $row,
                $item->product->name ?? '-'
            );

            $sheet->setCellValue(
                'E' . $row,
                $item->product->sku ?? '-'
            );

            $sheet->setCellValue(
                'F' . $row,
                $item->movement_type
            );

            $sheet->setCellValue(
                'G' . $row,
                $item->qty
            );

            $sheet->setCellValue(
                'H' . $row,
                $item->balance_after
            );

            $sheet->setCellValue(
                'I' . $row,
                $item->reference_type ?? '-'
            );

            $sheet->setCellValue(
                'J' . $row,
                $item->reference_id ?? '-'
            );

           $sheet->setCellValue(
                'K'.$row,
                $item->created_by_name
            );

            $row++;
        }

        // =====================================================
        // STYLE
        // =====================================================

        $sheet->getStyle('A1:K1')
            ->getFont()
            ->setBold(true);

        // =====================================================
        // AUTO SIZE
        // =====================================================

        foreach (range('A', 'K') as $column) {
            $sheet
                ->getColumnDimension($column)
                ->setAutoSize(true);
        }

        // Freeze header
        $sheet->freezePane('A2');

        // =====================================================
        // DOWNLOAD
        // =====================================================

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(
            function () use ($writer) {
                $writer->save('php://output');
            },
            $fileName
        );
    }
}