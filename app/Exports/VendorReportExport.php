<?php

namespace App\Exports;

use App\Services\ReportService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class VendorReportExport
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function download($fileName = 'vendor_report.xlsx')
    {
        $service = app(ReportService::class);
        $vendors = $service->getVendorReport($this->filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // HEADER
        $sheet->setCellValue('A1', 'SR');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Company');
        $sheet->setCellValue('D1', 'Mobile');
        $sheet->setCellValue('E1', 'Email');
        $sheet->setCellValue('F1', 'GST');
        $sheet->setCellValue('G1', 'City');
        $sheet->setCellValue('H1', 'State');
        $sheet->setCellValue('I1', 'Address');
        $sheet->setCellValue('J1', 'Total Purchases');
        $sheet->setCellValue('K1', 'Total Amount');

        $row = 2;
        $i = 1;

        foreach ($vendors as $v) {

            $sheet->setCellValue('A'.$row, $i++);
            $sheet->setCellValue('B'.$row, $v->name);
            $sheet->setCellValue('C'.$row, $v->company_name);
            $sheet->setCellValue('D'.$row, $v->mobile);
            $sheet->setCellValue('E'.$row, $v->email);
            $sheet->setCellValue('F'.$row, $v->gst_number);
            $sheet->setCellValue('G'.$row, $v->city);
            $sheet->setCellValue('H'.$row, $v->state);
            $sheet->setCellValue('I'.$row, $v->address ?? $v->billing_address);
            $sheet->setCellValue('J'.$row, $v->total_purchases);
            $sheet->setCellValue('K'.$row, $v->total_amount);

            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }
}