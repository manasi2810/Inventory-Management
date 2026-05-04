<?php

namespace App\Exports;

use App\Services\ReportService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DcReturnExport
{
    protected $filters;
    protected $service;

    public function __construct($filters)
    {
        $this->filters = $filters;
        $this->service = new ReportService();
    }

    public function download()
    {
        $data = $this->service->getDcReturnReport($this->filters);
        $summary = $this->service->getDcReturnSummary($this->filters);
        $records = $data->getCollection();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
 
        $sheet->setTitle('DC Return Report');
 

        $sheet->setCellValue('A1', 'RADCOM PACKAGING INDUSTRIES');
        $sheet->mergeCells('A1:G1');

        $sheet->setCellValue('A2', 'DC RETURN MASTER REPORT');
        $sheet->mergeCells('A2:G2');

        $sheet->setCellValue('A3',
            'From: ' . ($this->filters['from_date'] ?? '-') .
            ' To: ' . ($this->filters['to_date'] ?? '-')
        );
        $sheet->mergeCells('A3:G3');

        $sheet->setCellValue('A4', 'Generated: ' . now()->format('d-m-Y H:i'));
        $sheet->mergeCells('A4:G4');
 
        $sheet->getStyle("A1:A4")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
 

        $sheet->setCellValue('A6', 'SUMMARY');
        $sheet->mergeCells('A6:G6');

        $sheet->setCellValue('A7', 'Total Qty');     $sheet->setCellValue('B7', $summary->total ?? 0);
        $sheet->setCellValue('A8', 'Good');          $sheet->setCellValue('B8', $summary->good ?? 0);
        $sheet->setCellValue('A9', 'Damaged');       $sheet->setCellValue('B9', $summary->damaged ?? 0);
        $sheet->setCellValue('A10','Scrap');         $sheet->setCellValue('B10',$summary->scrap ?? 0);
 

        $startRow = 12;

        $headers = ['SR', 'Date', 'DC No', 'Customer', 'Product', 'Qty', 'Condition'];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col.$startRow, $header);
            $col++;
        }
 
        $sheet->getStyle("A$startRow:G$startRow")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E79']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);
 

        $row = $startRow + 1;
        $i = 1;
        $totalQty = 0;

        foreach ($records as $item) {

            $sheet->setCellValue("A$row", $i++);
            $sheet->setCellValue("B$row", $item->dcReturn->return_date ?? '-');
            $sheet->setCellValue("C$row", $item->dcReturn->deliveryChallan->challan_no ?? '-');
            $sheet->setCellValue("D$row", $item->dcReturn->deliveryChallan->customer->name ?? '-');
            $sheet->setCellValue("E$row", $item->product->name ?? '-');
            $sheet->setCellValue("F$row", $item->return_qty);
            $sheet->setCellValue("G$row", ucfirst($item->condition));

            $totalQty += $item->return_qty;
 
            $sheet->getStyle("A$row:G$row")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ]
                ]
            ]);

            $row++;
        }
 

        $sheet->setCellValue("E$row", "TOTAL");
        $sheet->setCellValue("F$row", $totalQty);

        $sheet->getStyle("E$row:F$row")->applyFromArray([
            'font' => ['bold' => true]
        ]);
 

        $widths = ['A'=>5,'B'=>15,'C'=>20,'D'=>25,'E'=>25,'F'=>10,'G'=>15];

        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }
 

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(
            function () use ($writer) {
                $writer->save('php://output');
            },
            'DC_Return_ERP_Report.xlsx'
        );
    }
}