<?php

namespace App\Exports;

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

        // =====================================================
        // HEADER
        // =====================================================

        $headers = [
            'A1' => '#',
            'B1' => 'Product ID',
            'C1' => 'Product',
            'D1' => 'SKU',
            'E1' => 'Category',

            'F1' => 'Opening Stock',

            'G1' => 'Purchase / Stock In',
            'H1' => 'Customer Returns',
            'I1' => 'Total Stock In',

            'J1' => 'Dispatch / Stock Out',
            'K1' => 'Total Stock Out',

            'L1' => 'Closing / Current Stock',
            'M1' => 'Status',

            'N1' => 'First Movement Date',
            'O1' => 'Last Movement Date',

            'P1' => 'Last Movement Type',
            'Q1' => 'Last Movement Qty',
            'R1' => 'Last Balance',

            'S1' => 'Last Reference Type',
            'T1' => 'Last Reference ID',
            'U1' => 'Last Created By',
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

            $closingStock = $item->closing_stock ?? 0;

            // Status
            if ($closingStock <= 0) {
                $status = 'Out of Stock';
            } elseif ($closingStock <= 10) {
                $status = 'Low Stock';
            } else {
                $status = 'In Stock';
            }

            $sheet->setCellValue('A' . $row, $i++);

            $sheet->setCellValue(
                'B' . $row,
                $item->id ?? ''
            );

            $sheet->setCellValue(
                'C' . $row,
                $item->name ?? ''
            );

            $sheet->setCellValue(
                'D' . $row,
                $item->sku ?? '-'
            );

            $sheet->setCellValue(
                'E' . $row,
                $item->category ?? '-'
            );

            // Opening stock
            $sheet->setCellValue(
                'F' . $row,
                $item->opening_stock ?? 0
            );

            // Stock IN
            $sheet->setCellValue(
                'G' . $row,
                $item->purchase_qty ?? 0
            );

            // Returns
            $sheet->setCellValue(
                'H' . $row,
                $item->return_qty ?? 0
            );

            // Total IN
            $sheet->setCellValue(
                'I' . $row,
                $item->total_in ?? 0
            );

            // Stock OUT
            $sheet->setCellValue(
                'J' . $row,
                $item->sale_qty ?? 0
            );

            // Total OUT
            $sheet->setCellValue(
                'K' . $row,
                $item->total_out ?? 0
            );

            // Current / Closing Stock
            $sheet->setCellValue(
                'L' . $row,
                $closingStock
            );

            // Status
            $sheet->setCellValue(
                'M' . $row,
                $item->status ?? $status
            );

            // First movement
            $sheet->setCellValue(
                'N' . $row,
                $item->first_movement_date ?? '-'
            );

            // Last movement
            $sheet->setCellValue(
                'O' . $row,
                $item->last_movement_date ?? '-'
            );

            // Last movement details
            $sheet->setCellValue(
                'P' . $row,
                $item->last_movement_type ?? '-'
            );

            $sheet->setCellValue(
                'Q' . $row,
                $item->last_movement_qty ?? 0
            );

            $sheet->setCellValue(
                'R' . $row,
                $item->last_balance ?? 0
            );

            // Reference
            $sheet->setCellValue(
                'S' . $row,
                $item->last_reference_type ?? '-'
            );

            $sheet->setCellValue(
                'T' . $row,
                $item->last_reference_id ?? '-'
            );

            // User
            $sheet->setCellValue(
                'U' . $row,
                $item->last_created_by ?? '-'
            );

            $row++;
        }

        // =====================================================
        // HEADER STYLE
        // =====================================================

        $sheet->getStyle('A1:U1')->getFont()->setBold(true);

        // =====================================================
        // AUTO SIZE
        // =====================================================

        foreach (range('A', 'U') as $column) {
            $sheet
                ->getColumnDimension($column)
                ->setAutoSize(true);
        }

        // =====================================================
        // FREEZE HEADER
        // =====================================================

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