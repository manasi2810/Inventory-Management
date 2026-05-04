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

        $dcList = $service->getDcReport($this->filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        /* ================= HEADER ================= */
        $sheet->setCellValue('A1', 'SR');
        $sheet->setCellValue('B1', 'DC No');
        $sheet->setCellValue('C1', 'Date');
        $sheet->setCellValue('D1', 'Customer');
        $sheet->setCellValue('E1', 'Total Qty');
        $sheet->setCellValue('F1', 'Amount');
        $sheet->setCellValue('G1', 'Status');

        $row = 2;
        $i = 1;

        foreach ($dcList as $dc) {

            $sheet->setCellValue('A'.$row, $i++);
            $sheet->setCellValue('B'.$row, $dc->challan_no ?? '-');
            $sheet->setCellValue('C'.$row, date('d-m-Y', strtotime($dc->challan_date)));
            $sheet->setCellValue('D'.$row, $dc->customer->name ?? '-');
            $sheet->setCellValue('E'.$row, $dc->total_qty ?? 0);
            $sheet->setCellValue('F'.$row, $dc->total_amount ?? 0);
            $sheet->setCellValue('G'.$row, ucfirst($dc->status));

            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }
}