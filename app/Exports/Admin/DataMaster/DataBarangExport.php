<?php

namespace App\Exports\Admin\DataMaster;

use App\Models\Admin\DataMaster\DataBarang;
use App\Models\Admin\Setting\IdentitasKoperasi;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Carbon\Carbon;

class DataBarangExport
{
    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("Sistem Koperasi")
            ->setTitle("Data Barang")
            ->setSubject("Export Data Barang")
            ->setDescription("Data Master Barang");

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
        $sheet->mergeCells('B1:G1');
        $sheet->setCellValue('B1', strtoupper($identitas->nama_lembaga ?? 'KOPERASI SIMPAN PINJAM'));
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->mergeCells('B2:G2');
        $sheet->setCellValue('B2', $identitas->alamat ?? 'Alamat Koperasi');
        $sheet->getStyle('B2')->getFont()->setSize(9);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->mergeCells('B3:G3');
        $contactInfo = 'Telp: ' . ($identitas->telepon ?? '-') . ' | Email: ' . ($identitas->email ?? '-');
        $sheet->setCellValue('B3', $contactInfo);
        $sheet->getStyle('B3')->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B3')->getFont()->getColor()->setRGB('555555');

        // Garis pembatas
        $sheet->mergeCells('A4:G4');
        $sheet->getStyle('A4:G4')->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUM)
            ->getColor()->setRGB('4472C4');

        // ====================================
        // TITLE SECTION
        // ====================================
        
        $sheet->mergeCells('A6:G6');
        $sheet->setCellValue('A6', 'LAPORAN DATA BARANG');
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A6')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E7F3FF');

        // Tanggal Export
        $sheet->mergeCells('A7:G7');
        $sheet->setCellValue('A7', 'Dicetak pada: ' . Carbon::now()->translatedFormat('d F Y H:i'));
        $sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A7')->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle('A7')->getFont()->getColor()->setRGB('666666');

        // ====================================
        // TABLE HEADER
        // ====================================
        
        $headerRow = 9;
        $headers = ['No', 'Nama Barang', 'Type', 'Merk', 'Harga (Rp)', 'Jumlah', 'Keterangan'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

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
        $sheet->getStyle('A' . $headerRow . ':G' . $headerRow)->applyFromArray($headerStyle);
        $sheet->getRowDimension($headerRow)->setRowHeight(25);

        // ====================================
        // DATA ROWS
        // ====================================
        
        $dataBarang = DataBarang::orderBy('nama_barang', 'asc')->get();

        $row = $headerRow + 1;
        $no = 1;
        $totalHarga = 0;
        $totalJumlah = 0;

        foreach ($dataBarang as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item->nama_barang);
            $sheet->setCellValue('C' . $row, $item->type ?? '-');
            $sheet->setCellValue('D' . $row, $item->merk ?? '-');
            $sheet->setCellValue('E' . $row, $item->harga);
            $sheet->setCellValue('F' . $row, $item->jumlah);
            $sheet->setCellValue('G' . $row, $item->keterangan ?? '-');

            // Format currency
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');

            // Alternating row colors
            if ($no % 2 == 0) {
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8F9FA');
            }

            $totalHarga += $item->harga * $item->jumlah;
            $totalJumlah += $item->jumlah;
            $no++;
            $row++;
        }

        // ====================================
        // BORDERS FOR DATA
        // ====================================
        
        $dataRange = 'A' . ($headerRow + 1) . ':G' . ($row - 1);
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
        $sheet->getStyle('E' . ($headerRow + 1) . ':E' . ($row - 1))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('F' . ($headerRow + 1) . ':F' . ($row - 1))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ====================================
        // FOOTER - TOTAL
        // ====================================
        
        $totalRow = $row;
        $sheet->setCellValue('A' . $totalRow, '');
        $sheet->setCellValue('B' . $totalRow, 'TOTAL');
        $sheet->setCellValue('C' . $totalRow, '');
        $sheet->setCellValue('D' . $totalRow, '');
        $sheet->setCellValue('E' . $totalRow, $totalHarga);
        $sheet->setCellValue('F' . $totalRow, $totalJumlah . ' unit');
        $sheet->setCellValue('G' . $totalRow, '');

        $footerStyle = [
            'font' => [
                'bold' => true,
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E7E6E6']
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
        
        $sheet->getStyle('A' . $totalRow . ':G' . $totalRow)->applyFromArray($footerStyle);
        $sheet->getStyle('B' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('E' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('E' . $totalRow)->getNumberFormat()->setFormatCode('#,##0');

        // ====================================
        // SUMMARY INFO
        // ====================================
        
        $summaryRow = $totalRow + 2;
        $sheet->mergeCells('A' . $summaryRow . ':G' . $summaryRow);
        $sheet->setCellValue('A' . $summaryRow, 'RINGKASAN DATA');
        $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A' . $summaryRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F8F9FA');
        $sheet->getStyle('A' . $summaryRow)->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_THIN);

        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Total Jenis Barang:');
        $sheet->setCellValue('C' . $summaryRow, $dataBarang->count() . ' jenis');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Total Unit Barang:');
        $sheet->setCellValue('C' . $summaryRow, $totalJumlah . ' unit');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Total Nilai Inventaris:');
        $sheet->setCellValue('C' . $summaryRow, 'Rp ' . number_format($totalHarga, 0, ',', '.'));

        $firstSummaryRow = $totalRow + 3;
        $sheet->getStyle('A' . $firstSummaryRow . ':A' . $summaryRow)->getFont()->setSize(9);
        $sheet->getStyle('C' . $firstSummaryRow . ':C' . $summaryRow)->getFont()->setBold(true)->setSize(9);

        // ====================================
        // COLUMN WIDTH
        // ====================================
        
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(30);

        // ====================================
        // FOOTER NOTE
        // ====================================
        
        $footerNoteRow = $summaryRow + 2;
        $sheet->mergeCells('A' . $footerNoteRow . ':G' . $footerNoteRow);
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
        $fileName = 'Laporan_Data_Barang_' . date('d-m-Y') . '.xlsx';
        $tempFile = sys_get_temp_dir() . '/' . $fileName;

        $writer->save($tempFile);

        return [
            'path' => $tempFile,
            'filename' => $fileName
        ];
    }
}