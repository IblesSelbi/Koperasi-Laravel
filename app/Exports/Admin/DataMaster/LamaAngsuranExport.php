<?php

namespace App\Exports\Admin\DataMaster;

use App\Models\Admin\DataMaster\LamaAngsuran;
use App\Models\Admin\Setting\IdentitasKoperasi;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Carbon\Carbon;

class LamaAngsuranExport
{
    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("Sistem Koperasi")
            ->setTitle("Data Lama Angsuran")
            ->setSubject("Export Data Lama Angsuran")
            ->setDescription("Data Master Lama Angsuran");

        // Get Identitas Koperasi
        $identitas = IdentitasKoperasi::first();

        // ====================================
        // HEADER SECTION - IDENTITAS KOPERASI
        // ====================================
        
        // Logo (jika ada)
        if ($identitas && $identitas->logo && file_exists(public_path($identitas->logo))) {
            try {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo Koperasi');
                $drawing->setPath(public_path($identitas->logo));
                $drawing->setHeight(60);
                $drawing->setCoordinates('A1');
                $drawing->setWorksheet($sheet);
            } catch (\Exception $e) {
                // Skip jika logo gagal dimuat
            }
        }

        // Identitas Koperasi
        $sheet->mergeCells('B1:D1');
        $sheet->setCellValue('B1', strtoupper($identitas->nama_lembaga ?? 'KOPERASI SIMPAN PINJAM'));
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->mergeCells('B2:D2');
        $sheet->setCellValue('B2', $identitas->alamat ?? 'Alamat Koperasi');
        $sheet->getStyle('B2')->getFont()->setSize(9);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->mergeCells('B3:D3');
        $contactInfo = 'Telp: ' . ($identitas->telepon ?? '-') . ' | Email: ' . ($identitas->email ?? '-');
        $sheet->setCellValue('B3', $contactInfo);
        $sheet->getStyle('B3')->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B3')->getFont()->getColor()->setRGB('555555');

        // Garis pembatas
        $sheet->mergeCells('A4:D4');
        $sheet->getStyle('A4:D4')->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUM)
            ->getColor()->setRGB('4472C4');

        // ====================================
        // TITLE SECTION
        // ====================================
        
        $sheet->mergeCells('A6:D6');
        $sheet->setCellValue('A6', 'LAPORAN DATA LAMA ANGSURAN');
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A6')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E7F3FF');

        // Tanggal Export
        $sheet->mergeCells('A7:D7');
        $sheet->setCellValue('A7', 'Dicetak pada: ' . Carbon::now()->translatedFormat('d F Y H:i'));
        $sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A7')->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle('A7')->getFont()->getColor()->setRGB('666666');

        // ====================================
        // TABLE HEADER
        // ====================================
        
        $headerRow = 9;
        $headers = ['No', 'Lama Angsuran (Bulan)', 'Status Aktif', 'Keterangan'];
        $columns = ['A', 'B', 'C', 'D'];

        foreach ($columns as $index => $column) {
            $sheet->setCellValue($column . $headerRow, $headers[$index]);
        }

        // Style Header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ]
        ];
        $sheet->getStyle('A' . $headerRow . ':D' . $headerRow)->applyFromArray($headerStyle);
        $sheet->getRowDimension($headerRow)->setRowHeight(25);

        // ====================================
        // DATA ROWS
        // ====================================
        
        $lamaAngsuran = LamaAngsuran::orderBy('lama_angsuran', 'asc')->get();

        $row = $headerRow + 1;
        $no = 1;

        foreach ($lamaAngsuran as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item->lama_angsuran . ' Bulan');
            $sheet->setCellValue('C' . $row, $item->aktif);
            $sheet->setCellValue('D' . $row, $item->aktif == 'Y' ? 'Aktif' : 'Tidak Aktif');

            // Alternating row colors
            if ($no % 2 == 0) {
                $sheet->getStyle('A' . $row . ':D' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8F9FA');
            }

            $no++;
            $row++;
        }

        // ====================================
        // BORDERS FOR DATA
        // ====================================
        
        $dataRange = 'A' . ($headerRow + 1) . ':D' . ($row - 1);
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD']
                ]
            ]
        ];
        $sheet->getStyle($dataRange)->applyFromArray($dataStyle);

        // Alignment
        $sheet->getStyle('A' . ($headerRow + 1) . ':A' . ($row - 1))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B' . ($headerRow + 1) . ':B' . ($row - 1))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C' . ($headerRow + 1) . ':D' . ($row - 1))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ====================================
        // SUMMARY INFO
        // ====================================
        
        $summaryRow = $row + 1;
        $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
        $sheet->setCellValue('A' . $summaryRow, 'RINGKASAN DATA');
        $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A' . $summaryRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F8F9FA');
        $sheet->getStyle('A' . $summaryRow)->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_THIN);

        $summaryRow++;
        
        // Hitung statistik
        $totalAngsuran = $lamaAngsuran->count();
        $aktifY = $lamaAngsuran->where('aktif', 'Y')->count();
        $aktifT = $lamaAngsuran->where('aktif', 'T')->count();
        $minBulan = $lamaAngsuran->min('lama_angsuran');
        $maxBulan = $lamaAngsuran->max('lama_angsuran');

        $sheet->setCellValue('A' . $summaryRow, '• Total Pilihan Angsuran:');
        $sheet->setCellValue('C' . $summaryRow, $totalAngsuran . ' pilihan');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Status Aktif (Y):');
        $sheet->setCellValue('C' . $summaryRow, $aktifY . ' pilihan');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Status Tidak Aktif (T):');
        $sheet->setCellValue('C' . $summaryRow, $aktifT . ' pilihan');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Angsuran Minimum:');
        $sheet->setCellValue('C' . $summaryRow, ($minBulan ?? 0) . ' bulan');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Angsuran Maximum:');
        $sheet->setCellValue('C' . $summaryRow, ($maxBulan ?? 0) . ' bulan');

        $firstSummaryRow = $row + 2;
        $sheet->getStyle('A' . $firstSummaryRow . ':A' . $summaryRow)->getFont()->setSize(9);
        $sheet->getStyle('C' . $firstSummaryRow . ':C' . $summaryRow)->getFont()->setBold(true)->setSize(9);

        // ====================================
        // COLUMN WIDTH
        // ====================================
        
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(20);

        // ====================================
        // FOOTER NOTE
        // ====================================
        
        $footerNoteRow = $summaryRow + 2;
        $sheet->mergeCells('A' . $footerNoteRow . ':D' . $footerNoteRow);
        $sheet->setCellValue('A' . $footerNoteRow, '© ' . date('Y') . ' ' . ($identitas->nama_lembaga ?? 'Koperasi') . ' - Dicetak dari Sistem Koperasi');
        $sheet->getStyle('A' . $footerNoteRow)->getFont()->setSize(8)->setItalic(true);
        $sheet->getStyle('A' . $footerNoteRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $footerNoteRow)->getFont()->getColor()->setRGB('999999');

        // ====================================
        // FREEZE PANES
        // ====================================
        
        $sheet->freezePane('A' . ($headerRow + 1));

        // ====================================
        // GENERATE FILE
        // ====================================
        
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan_Lama_Angsuran_' . date('d-m-Y') . '.xlsx';
        $tempFile = sys_get_temp_dir() . '/' . $fileName;

        $writer->save($tempFile);

        return [
            'path' => $tempFile,
            'filename' => $fileName
        ];
    }
}