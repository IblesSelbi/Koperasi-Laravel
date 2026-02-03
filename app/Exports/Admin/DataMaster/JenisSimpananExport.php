<?php

namespace App\Exports\Admin\DataMaster;

use App\Models\Admin\DataMaster\JenisSimpanan;
use App\Models\Admin\Setting\IdentitasKoperasi;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Carbon\Carbon;

class JenisSimpananExport
{
    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("Sistem Koperasi")
            ->setTitle("Data Jenis Simpanan")
            ->setSubject("Export Data Jenis Simpanan")
            ->setDescription("Data Master Jenis Simpanan");

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
        $sheet->mergeCells('B1:E1');
        $sheet->setCellValue('B1', strtoupper($identitas->nama_lembaga ?? 'KOPERASI SIMPAN PINJAM'));
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->mergeCells('B2:E2');
        $sheet->setCellValue('B2', $identitas->alamat ?? 'Alamat Koperasi');
        $sheet->getStyle('B2')->getFont()->setSize(9);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->mergeCells('B3:E3');
        $contactInfo = 'Telp: ' . ($identitas->telepon ?? '-') . ' | Email: ' . ($identitas->email ?? '-');
        $sheet->setCellValue('B3', $contactInfo);
        $sheet->getStyle('B3')->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B3')->getFont()->getColor()->setRGB('555555');

        // Garis pembatas
        $sheet->mergeCells('A4:E4');
        $sheet->getStyle('A4:E4')->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUM)
            ->getColor()->setRGB('4472C4');

        // ====================================
        // TITLE SECTION
        // ====================================
        
        $sheet->mergeCells('A6:E6');
        $sheet->setCellValue('A6', 'LAPORAN DATA JENIS SIMPANAN');
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A6')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E7F3FF');

        // Tanggal Export
        $sheet->mergeCells('A7:E7');
        $sheet->setCellValue('A7', 'Dicetak pada: ' . Carbon::now()->translatedFormat('d F Y H:i'));
        $sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A7')->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle('A7')->getFont()->getColor()->setRGB('666666');

        // ====================================
        // TABLE HEADER
        // ====================================
        
        $headerRow = 9;
        $headers = ['No', 'Jenis Simpanan', 'Jumlah (Rp)', 'Tampil', 'Keterangan'];
        $columns = ['A', 'B', 'C', 'D', 'E'];

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
        $sheet->getStyle('A' . $headerRow . ':E' . $headerRow)->applyFromArray($headerStyle);
        $sheet->getRowDimension($headerRow)->setRowHeight(25);

        // ====================================
        // DATA ROWS
        // ====================================
        
        $jenisSimpanan = JenisSimpanan::orderBy('jenis_simpanan', 'asc')->get();

        $row = $headerRow + 1;
        $no = 1;
        $totalJumlah = 0;

        foreach ($jenisSimpanan as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item->jenis_simpanan);
            $sheet->setCellValue('C' . $row, $item->jumlah);
            $sheet->setCellValue('D' . $row, $item->tampil); // Y atau T
            $sheet->setCellValue('E' . $row, $item->tampil == 'Y' ? 'Aktif' : 'Tidak Aktif');

            // Format currency
            $sheet->getStyle('C' . $row)->getNumberFormat()
                ->setFormatCode('#,##0');

            // Alternating row colors
            if ($no % 2 == 0) {
                $sheet->getStyle('A' . $row . ':E' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8F9FA');
            }

            $totalJumlah += $item->jumlah;
            $no++;
            $row++;
        }

        // ====================================
        // BORDERS FOR DATA
        // ====================================
        
        $dataRange = 'A' . ($headerRow + 1) . ':E' . ($row - 1);
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
        $sheet->getStyle('C' . ($headerRow + 1) . ':C' . ($row - 1))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('D' . ($headerRow + 1) . ':D' . ($row - 1))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ====================================
        // FOOTER - TOTAL
        // ====================================
        
        $totalRow = $row;
        $sheet->setCellValue('A' . $totalRow, '');
        $sheet->setCellValue('B' . $totalRow, 'TOTAL KESELURUHAN');
        $sheet->setCellValue('C' . $totalRow, $totalJumlah);
        $sheet->setCellValue('D' . $totalRow, '');
        $sheet->setCellValue('E' . $totalRow, $jenisSimpanan->count() . ' Jenis');

        $footerStyle = [
            'font' => [
                'bold' => true,
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E7E6E6'] // Abu-abu soft
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        
        $sheet->getStyle('A' . $totalRow . ':E' . $totalRow)->applyFromArray($footerStyle);
        $sheet->getStyle('B' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('C' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('C' . $totalRow)->getNumberFormat()->setFormatCode('#,##0');

        // ====================================
        // SUMMARY INFO
        // ====================================
        
        $summaryRow = $totalRow + 2;
        $sheet->mergeCells('A' . $summaryRow . ':E' . $summaryRow);
        $sheet->setCellValue('A' . $summaryRow, 'RINGKASAN DATA');
        $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A' . $summaryRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F8F9FA');
        $sheet->getStyle('A' . $summaryRow)->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_THIN);

        $summaryRow++;
        $tampilY = $jenisSimpanan->where('tampil', 'Y')->count();
        $tampilT = $jenisSimpanan->where('tampil', 'T')->count();

        $sheet->setCellValue('A' . $summaryRow, '• Total Jenis Simpanan:');
        $sheet->setCellValue('C' . $summaryRow, $jenisSimpanan->count() . ' jenis');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Total Nominal Simpanan:');
        $sheet->setCellValue('C' . $summaryRow, 'Rp ' . number_format($totalJumlah, 0, ',', '.'));
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Status Tampil (Y):');
        $sheet->setCellValue('C' . $summaryRow, $tampilY . ' jenis');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Status Tidak Tampil (T):');
        $sheet->setCellValue('C' . $summaryRow, $tampilT . ' jenis');

        $sheet->getStyle('A' . ($totalRow + 3) . ':A' . $summaryRow)->getFont()->setSize(9);
        $sheet->getStyle('C' . ($totalRow + 3) . ':C' . $summaryRow)->getFont()->setBold(true)->setSize(9);

        // ====================================
        // COLUMN WIDTH
        // ====================================
        
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(25);

        // ====================================
        // FOOTER NOTE
        // ====================================
        
        $footerNoteRow = $summaryRow + 2;
        $sheet->mergeCells('A' . $footerNoteRow . ':E' . $footerNoteRow);
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
        $fileName = 'Laporan_Jenis_Simpanan_' . date('d-m-Y') . '.xlsx';
        $tempFile = sys_get_temp_dir() . '/' . $fileName;

        $writer->save($tempFile);

        return [
            'path' => $tempFile,
            'filename' => $fileName
        ];
    }
}