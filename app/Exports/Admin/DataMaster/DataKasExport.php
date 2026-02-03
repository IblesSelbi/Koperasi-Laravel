<?php

namespace App\Exports\Admin\DataMaster;

use App\Models\Admin\DataMaster\DataKas;
use App\Models\Admin\Setting\IdentitasKoperasi;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Carbon\Carbon;

class DataKasExport
{
    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("Sistem Koperasi")
            ->setTitle("Data Kas")
            ->setSubject("Export Data Kas")
            ->setDescription("Data Master Kas");

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
        $sheet->mergeCells('B1:J1');
        $sheet->setCellValue('B1', strtoupper($identitas->nama_lembaga ?? 'KOPERASI SIMPAN PINJAM'));
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->mergeCells('B2:J2');
        $sheet->setCellValue('B2', $identitas->alamat ?? 'Alamat Koperasi');
        $sheet->getStyle('B2')->getFont()->setSize(9);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->mergeCells('B3:J3');
        $contactInfo = 'Telp: ' . ($identitas->telepon ?? '-') . ' | Email: ' . ($identitas->email ?? '-');
        $sheet->setCellValue('B3', $contactInfo);
        $sheet->getStyle('B3')->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B3')->getFont()->getColor()->setRGB('555555');

        // Garis pembatas
        $sheet->mergeCells('A4:J4');
        $sheet->getStyle('A4:J4')->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUM)
            ->getColor()->setRGB('4472C4');

        // ====================================
        // TITLE SECTION
        // ====================================
        
        $sheet->mergeCells('A6:J6');
        $sheet->setCellValue('A6', 'LAPORAN DATA KAS');
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A6')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E7F3FF');

        // Tanggal Export
        $sheet->mergeCells('A7:J7');
        $sheet->setCellValue('A7', 'Dicetak pada: ' . Carbon::now()->translatedFormat('d F Y H:i'));
        $sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A7')->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle('A7')->getFont()->getColor()->setRGB('666666');

        // ====================================
        // TABLE HEADER
        // ====================================
        
        $headerRow = 9;
        $headers = ['No', 'Nama Kas', 'Aktif', 'Simpanan', 'Penarikan', 'Pinjaman', 'Angsuran', 'Pemasukan Kas', 'Pengeluaran Kas', 'Transfer Kas'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];

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
        $sheet->getStyle('A' . $headerRow . ':J' . $headerRow)->applyFromArray($headerStyle);
        $sheet->getRowDimension($headerRow)->setRowHeight(25);

        // ====================================
        // DATA ROWS
        // ====================================
        
        $dataKas = DataKas::orderBy('nama_kas', 'asc')->get();

        $row = $headerRow + 1;
        $no = 1;

        foreach ($dataKas as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item->nama_kas);
            $sheet->setCellValue('C' . $row, $item->aktif);
            $sheet->setCellValue('D' . $row, $item->simpanan);
            $sheet->setCellValue('E' . $row, $item->penarikan);
            $sheet->setCellValue('F' . $row, $item->pinjaman);
            $sheet->setCellValue('G' . $row, $item->angsuran);
            $sheet->setCellValue('H' . $row, $item->pemasukan_kas);
            $sheet->setCellValue('I' . $row, $item->pengeluaran_kas);
            $sheet->setCellValue('J' . $row, $item->transfer_kas);

            // Alternating row colors
            if ($no % 2 == 0) {
                $sheet->getStyle('A' . $row . ':J' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8F9FA');
            }

            $no++;
            $row++;
        }

        // ====================================
        // BORDERS FOR DATA
        // ====================================
        
        $dataRange = 'A' . ($headerRow + 1) . ':J' . ($row - 1);
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
        $sheet->getStyle('C' . ($headerRow + 1) . ':J' . ($row - 1))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ====================================
        // SUMMARY INFO
        // ====================================
        
        $summaryRow = $row + 1;
        $sheet->mergeCells('A' . $summaryRow . ':J' . $summaryRow);
        $sheet->setCellValue('A' . $summaryRow, 'RINGKASAN DATA');
        $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A' . $summaryRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F8F9FA');
        $sheet->getStyle('A' . $summaryRow)->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_THIN);

        $summaryRow++;
        
        // Hitung statistik
        $totalKas = $dataKas->count();
        $aktifY = $dataKas->where('aktif', 'Y')->count();
        $simpananY = $dataKas->where('simpanan', 'Y')->count();
        $penarikanY = $dataKas->where('penarikan', 'Y')->count();
        $pinjamanY = $dataKas->where('pinjaman', 'Y')->count();
        $angsuranY = $dataKas->where('angsuran', 'Y')->count();
        $pemasukanY = $dataKas->where('pemasukan_kas', 'Y')->count();
        $pengeluaranY = $dataKas->where('pengeluaran_kas', 'Y')->count();
        $transferY = $dataKas->where('transfer_kas', 'Y')->count();

        $sheet->setCellValue('A' . $summaryRow, '• Total Kas:');
        $sheet->setCellValue('C' . $summaryRow, $totalKas . ' kas');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Status Aktif (Y):');
        $sheet->setCellValue('C' . $summaryRow, $aktifY . ' kas');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Simpanan Aktif (Y):');
        $sheet->setCellValue('C' . $summaryRow, $simpananY . ' kas');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Penarikan Aktif (Y):');
        $sheet->setCellValue('C' . $summaryRow, $penarikanY . ' kas');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Pinjaman Aktif (Y):');
        $sheet->setCellValue('C' . $summaryRow, $pinjamanY . ' kas');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Angsuran Aktif (Y):');
        $sheet->setCellValue('C' . $summaryRow, $angsuranY . ' kas');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Pemasukan Kas Aktif (Y):');
        $sheet->setCellValue('C' . $summaryRow, $pemasukanY . ' kas');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Pengeluaran Kas Aktif (Y):');
        $sheet->setCellValue('C' . $summaryRow, $pengeluaranY . ' kas');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Transfer Kas Aktif (Y):');
        $sheet->setCellValue('C' . $summaryRow, $transferY . ' kas');

        $firstSummaryRow = $row + 2;
        $sheet->getStyle('A' . $firstSummaryRow . ':A' . $summaryRow)->getFont()->setSize(9);
        $sheet->getStyle('C' . $firstSummaryRow . ':C' . $summaryRow)->getFont()->setBold(true)->setSize(9);

        // ====================================
        // COLUMN WIDTH
        // ====================================
        
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(16);
        $sheet->getColumnDimension('I')->setWidth(16);
        $sheet->getColumnDimension('J')->setWidth(14);

        // ====================================
        // FOOTER NOTE
        // ====================================
        
        $footerNoteRow = $summaryRow + 2;
        $sheet->mergeCells('A' . $footerNoteRow . ':J' . $footerNoteRow);
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
        $fileName = 'Laporan_Data_Kas_' . date('d-m-Y') . '.xlsx';
        $tempFile = sys_get_temp_dir() . '/' . $fileName;

        $writer->save($tempFile);

        return [
            'path' => $tempFile,
            'filename' => $fileName
        ];
    }
}