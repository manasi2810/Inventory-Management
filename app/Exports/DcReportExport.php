<?php

namespace App\Exports;

use App\Services\ReportService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DcReportExport
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function download($fileName = 'dc_report.xlsx')
    {
        $service = app(ReportService::class);

        $dcList = $service->getDcReportForExport($this->filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        /* ================= HEADER ================= */

        $headers = [
            'SR',
            'DC No',
            'Date',
            'Customer',
            'Product',
            'Product Code',
            'Qty',
            'Rate',
            'Amount',
            'Status',
        ];

        foreach ($headers as $index => $header) {
            $column = chr(65 + $index);

            $sheet->setCellValue(
                $column . '1',
                $header
            );
        }

        /* ================= DATA ================= */

        $row = 2;
        $i = 1;

        foreach ($dcList as $dc) {

            foreach ($dc->items as $item) {

                $sheet->setCellValue(
                    'A' . $row,
                    $i++
                );

                $sheet->setCellValue(
                    'B' . $row,
                    $dc->challan_no ?? '-'
                );

                $sheet->setCellValue(
                    'C' . $row,
                    $dc->challan_date
                        ? $dc->challan_date->format('d-m-Y')
                        : '-'
                );

                $sheet->setCellValue(
                    'D' . $row,
                    $dc->customer->name ?? '-'
                );

                $sheet->setCellValue(
                    'E' . $row,
                    $item->product->name ?? '-'
                );

                $sheet->setCellValue(
                    'F' . $row,
                    $item->product->sku ?? '-'
                );

                $sheet->setCellValue(
                    'G' . $row,
                    $item->qty ?? 0
                );

                $sheet->setCellValue(
                    'H' . $row,
                    $item->rate ?? 0
                );

                $sheet->setCellValue(
                    'I' . $row,
                    $item->total ?? 0
                );

                $sheet->setCellValue(
                    'J' . $row,
                    ucfirst($dc->status ?? '-')
                );

                $row++;
            }
        }

        /* ================= AUTO SIZE ================= */

        foreach (range('A', 'J') as $column) {
            $sheet
                ->getColumnDimension($column)
                ->setAutoSize(true);
        }

        /* ================= DOWNLOAD ================= */

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }
}