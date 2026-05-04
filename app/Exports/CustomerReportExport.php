<?php

namespace App\Exports;

use App\Services\ReportService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CustomerReportExport
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function download($fileName = 'customer_report.xlsx')
    {
        $service = app(ReportService::class);
        $customers = $service->getCustomerReport($this->filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ================= HEADER =================
        $sheet->setCellValue('A1', 'SR');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Company Name');
        $sheet->setCellValue('D1', 'Mobile');
        $sheet->setCellValue('E1', 'Alternate Mobile');
        $sheet->setCellValue('F1', 'Email');
        $sheet->setCellValue('G1', 'Billing Address');
        $sheet->setCellValue('H1', 'Shipping Address');
        $sheet->setCellValue('I1', 'City');
        $sheet->setCellValue('J1', 'State');
        $sheet->setCellValue('K1', 'Pincode');
        $sheet->setCellValue('L1', 'Country');
        $sheet->setCellValue('M1', 'GST Number');
        $sheet->setCellValue('N1', 'PAN Number');
        $sheet->setCellValue('O1', 'Customer Type');
        $sheet->setCellValue('P1', 'Status');
        $sheet->setCellValue('Q1', 'Created Date');

        $row = 2;
        $i = 1;

        foreach ($customers as $c) {

            $sheet->setCellValue('A' . $row, $i++);
            $sheet->setCellValue('B' . $row, $c->name ?? '-');
            $sheet->setCellValue('C' . $row, $c->company_name ?? '-');
            $sheet->setCellValue('D' . $row, $c->mobile ?? '-');
            $sheet->setCellValue('E' . $row, $c->alternate_mobile ?? '-');
            $sheet->setCellValue('F' . $row, $c->email ?? '-');

            $sheet->setCellValue('G' . $row, $c->billing_address ?? '-');
            $sheet->setCellValue('H' . $row, $c->shipping_address ?? '-');

            $sheet->setCellValue('I' . $row, $c->city ?? '-');
            $sheet->setCellValue('J' . $row, $c->state ?? '-');
            $sheet->setCellValue('K' . $row, $c->pincode ?? '-');
            $sheet->setCellValue('L' . $row, $c->country ?? '-');

            $sheet->setCellValue('M' . $row, $c->gst_number ?? '-');
            $sheet->setCellValue('N' . $row, $c->pan_number ?? '-');

            $sheet->setCellValue('O' . $row, ucfirst($c->customer_type ?? '-'));

            $sheet->setCellValue('P' . $row, $c->status == 1 ? 'Active' : 'Inactive');

            $sheet->setCellValue(
                'Q' . $row,
                $c->created_at ? date('d-m-Y', strtotime($c->created_at)) : '-'
            );

            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }
}