<?php

namespace App\Exports\Admin\DataMaster;

use App\Models\Admin\DataMaster\JenisAkun;
use App\Models\Admin\Setting\IdentitasKoperasi;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Carbon\Carbon;

class JenisAkunExport
{
    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("Sistem Koperasi")
            ->setTitle("Data Jenis Akun")
            ->setSubject("Export Data Jenis Akun")
            ->setDescription("Data Master Jenis Akun");

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

        // Identitas Koperasi (di sebelah logo)
        $sheet->mergeCells('B1:H1');
        $sheet->setCellValue('B1', strtoupper($identitas->nama_lembaga ?? 'KOPERASI SIMPAN PINJAM'));
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->mergeCells('B2:H2');
        $sheet->setCellValue('B2', $identitas->alamat ?? 'Alamat Koperasi');
        $sheet->getStyle('B2')->getFont()->setSize(9);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->mergeCells('B3:H3');
        $contactInfo = 'Telp: ' . ($identitas->telepon ?? '-') . ' | Email: ' . ($identitas->email ?? '-');
        $sheet->setCellValue('B3', $contactInfo);
        $sheet->getStyle('B3')->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B3')->getFont()->getColor()->setRGB('555555');

        // Garis pembatas
        $sheet->mergeCells('A4:H4');
        $sheet->getStyle('A4:H4')->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUM)
            ->getColor()->setRGB('4472C4');

        // ====================================
        // TITLE SECTION
        // ====================================
        
        $sheet->mergeCells('A6:H6');
        $sheet->setCellValue('A6', 'LAPORAN DATA JENIS AKUN');
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A6')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E7F3FF');

        // Tanggal Export
        $sheet->mergeCells('A7:H7');
        $sheet->setCellValue('A7', 'Dicetak pada: ' . Carbon::now()->translatedFormat('d F Y H:i'));
        $sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A7')->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle('A7')->getFont()->getColor()->setRGB('666666');

        // ====================================
        // TABLE HEADER
        // ====================================
        
        $headerRow = 9;
        $headers = ['No', 'Kode Aktiva', 'Jenis Transaksi', 'Akun', 'Pemasukan', 'Pengeluaran', 'Aktif', 'Laba Rugi'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        foreach ($columns as $index => $column) {
            $sheet->setCellValue($column . $headerRow, $headers[$index]);
        }

        // Style Header - Modern Blue
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
        $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->applyFromArray($headerStyle);
        $sheet->getRowDimension($headerRow)->setRowHeight(25);

        // ====================================
        // DATA ROWS
        // ====================================
        
        $jenisAkun = JenisAkun::orderBy('kd_aktiva', 'asc')->get();

        $row = $headerRow + 1;
        $no = 1;

        foreach ($jenisAkun as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item->kd_aktiva);
            $sheet->setCellValue('C' . $row, $item->jns_transaksi);
            $sheet->setCellValue('D' . $row, $item->akun);
            $sheet->setCellValue('E' . $row, $item->pemasukan);
            $sheet->setCellValue('F' . $row, $item->pengeluaran);
            $sheet->setCellValue('G' . $row, $item->aktif);
            $sheet->setCellValue('H' . $row, $item->laba_rugi ?? '-');

            // Alternating row colors (subtle gray)
            if ($no % 2 == 0) {
                $sheet->getStyle('A' . $row . ':H' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8F9FA');
            }

            $no++;
            $row++;
        }

        // ====================================
        // BORDERS FOR DATA
        // ====================================
        
        $dataRange = 'A' . ($headerRow + 1) . ':H' . ($row - 1);
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
        $sheet->getStyle('D' . ($headerRow + 1) . ':H' . ($row - 1))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ====================================
        // SUMMARY INFO
        // ====================================
        
        $summaryRow = $row + 1;
        $sheet->mergeCells('A' . $summaryRow . ':H' . $summaryRow);
        $sheet->setCellValue('A' . $summaryRow, 'RINGKASAN DATA');
        $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A' . $summaryRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F8F9FA');
        $sheet->getStyle('A' . $summaryRow)->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_THIN);

        $summaryRow++;
        
        // Hitung statistik
        $totalAkun = $jenisAkun->count();
        $aktiva = $jenisAkun->where('akun', 'Aktiva')->count();
        $pasiva = $jenisAkun->where('akun', 'Pasiva')->count();
        $pemasukanY = $jenisAkun->where('pemasukan', 'Y')->count();
        $pengeluaranY = $jenisAkun->where('pengeluaran', 'Y')->count();
        $aktifY = $jenisAkun->where('aktif', 'Y')->count();
        $pendapatan = $jenisAkun->where('laba_rugi', 'PENDAPATAN')->count();
        $biaya = $jenisAkun->where('laba_rugi', 'BIAYA')->count();

        $sheet->setCellValue('A' . $summaryRow, '• Total Jenis Akun:');
        $sheet->setCellValue('C' . $summaryRow, $totalAkun . ' akun');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Akun Aktiva:');
        $sheet->setCellValue('C' . $summaryRow, $aktiva . ' akun');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Akun Pasiva:');
        $sheet->setCellValue('C' . $summaryRow, $pasiva . ' akun');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Pemasukan Aktif (Y):');
        $sheet->setCellValue('C' . $summaryRow, $pemasukanY . ' akun');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Pengeluaran Aktif (Y):');
        $sheet->setCellValue('C' . $summaryRow, $pengeluaranY . ' akun');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Status Aktif (Y):');
        $sheet->setCellValue('C' . $summaryRow, $aktifY . ' akun');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Kategori Pendapatan:');
        $sheet->setCellValue('C' . $summaryRow, $pendapatan . ' akun');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Kategori Biaya:');
        $sheet->setCellValue('C' . $summaryRow, $biaya . ' akun');

        $firstSummaryRow = $row + 2;
        $sheet->getStyle('A' . $firstSummaryRow . ':A' . $summaryRow)->getFont()->setSize(9);
        $sheet->getStyle('C' . $firstSummaryRow . ':C' . $summaryRow)->getFont()->setBold(true)->setSize(9);

        // ====================================
        // COLUMN WIDTH
        // ====================================
        
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(14);
        $sheet->getColumnDimension('F')->setWidth(14);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(18);

        // ====================================
        // FOOTER NOTE
        // ====================================
        
        $footerNoteRow = $summaryRow + 2;
        $sheet->mergeCells('A' . $footerNoteRow . ':H' . $footerNoteRow);
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
        $fileName = 'Laporan_Jenis_Akun_' . date('d-m-Y') . '.xlsx';
        $tempFile = sys_get_temp_dir() . '/' . $fileName;

        $writer->save($tempFile);

        return [
            'path' => $tempFile,
            'filename' => $fileName
        ];
    }
}