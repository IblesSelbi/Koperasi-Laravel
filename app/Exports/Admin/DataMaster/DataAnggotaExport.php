<?php

namespace App\Exports\Admin\DataMaster;

use App\Models\Admin\DataMaster\DataAnggota;
use App\Models\Admin\Setting\IdentitasKoperasi;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Carbon\Carbon;

class DataAnggotaExport
{
    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("Sistem Koperasi")
            ->setTitle("Data Anggota")
            ->setSubject("Export Data Anggota")
            ->setDescription("Data Master Anggota Koperasi");

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
        $sheet->mergeCells('B1:M1');
        $sheet->setCellValue('B1', strtoupper($identitas->nama_lembaga ?? 'KOPERASI SIMPAN PINJAM'));
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->mergeCells('B2:M2');
        $sheet->setCellValue('B2', $identitas->alamat ?? 'Alamat Koperasi');
        $sheet->getStyle('B2')->getFont()->setSize(9);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->mergeCells('B3:M3');
        $contactInfo = 'Telp: ' . ($identitas->telepon ?? '-') . ' | Email: ' . ($identitas->email ?? '-');
        $sheet->setCellValue('B3', $contactInfo);
        $sheet->getStyle('B3')->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B3')->getFont()->getColor()->setRGB('555555');

        // Garis pembatas
        $sheet->mergeCells('A4:M4');
        $sheet->getStyle('A4:M4')->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUM)
            ->getColor()->setRGB('4472C4');

        // ====================================
        // TITLE SECTION
        // ====================================
        
        $sheet->mergeCells('A6:M6');
        $sheet->setCellValue('A6', 'LAPORAN DATA ANGGOTA KOPERASI');
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A6')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E7F3FF');

        // Tanggal Export
        $sheet->mergeCells('A7:M7');
        $sheet->setCellValue('A7', 'Dicetak pada: ' . Carbon::now()->translatedFormat('d F Y H:i'));
        $sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A7')->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle('A7')->getFont()->getColor()->setRGB('666666');

        // ====================================
        // TABLE HEADER
        // ====================================
        
        $headerRow = 9;
        $headers = [
            'No', 
            'ID Anggota', 
            'Username', 
            'Nama Lengkap', 
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Alamat',
            'Kota',
            'No. Telepon',
            'Departement',
            'Jabatan',
            'Status Aktif'
        ];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];

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
        $sheet->getStyle('A' . $headerRow . ':M' . $headerRow)->applyFromArray($headerStyle);
        $sheet->getRowDimension($headerRow)->setRowHeight(25);

        // ====================================
        // DATA ROWS
        // ====================================
        
        $dataAnggota = DataAnggota::orderBy('id_anggota', 'asc')->get();

        $row = $headerRow + 1;
        $no = 1;

        foreach ($dataAnggota as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item->id_anggota);
            $sheet->setCellValue('C' . $row, $item->username);
            $sheet->setCellValue('D' . $row, $item->nama);
            $sheet->setCellValue('E' . $row, $item->jenis_kelamin);
            $sheet->setCellValue('F' . $row, $item->tempat_lahir ?? '-');
            $sheet->setCellValue('G' . $row, $item->tanggal_lahir ? Carbon::parse($item->tanggal_lahir)->format('d/m/Y') : '-');
            $sheet->setCellValue('H' . $row, $item->alamat);
            $sheet->setCellValue('I' . $row, $item->kota);
            $sheet->setCellValue('J' . $row, $item->no_telp ?? '-');
            $sheet->setCellValue('K' . $row, $item->departement ?? '-');
            $sheet->setCellValue('L' . $row, $item->jabatan);
            $sheet->setCellValue('M' . $row, $item->aktif);

            // Alternating row colors
            if ($no % 2 == 0) {
                $sheet->getStyle('A' . $row . ':M' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8F9FA');
            }

            $no++;
            $row++;
        }

        // ====================================
        // BORDERS FOR DATA
        // ====================================
        
        $dataRange = 'A' . ($headerRow + 1) . ':M' . ($row - 1);
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
        $sheet->getStyle('E' . ($headerRow + 1) . ':E' . ($row - 1))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G' . ($headerRow + 1) . ':G' . ($row - 1))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('L' . ($headerRow + 1) . ':M' . ($row - 1))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ====================================
        // SUMMARY INFO
        // ====================================
        
        $summaryRow = $row + 1;
        $sheet->mergeCells('A' . $summaryRow . ':M' . $summaryRow);
        $sheet->setCellValue('A' . $summaryRow, 'RINGKASAN DATA');
        $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A' . $summaryRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F8F9FA');
        $sheet->getStyle('A' . $summaryRow)->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_THIN);

        $summaryRow++;
        
        // Hitung statistik
        $totalAnggota = $dataAnggota->count();
        $statusAktif = $dataAnggota->where('aktif', 'Aktif')->count();
        $statusNonAktif = $dataAnggota->where('aktif', 'Non Aktif')->count();
        $jabatanAnggota = $dataAnggota->where('jabatan', 'Anggota')->count();
        $jabatanPengurus = $dataAnggota->where('jabatan', 'Pengurus')->count();
        $lakiLaki = $dataAnggota->where('jenis_kelamin', 'Laki-laki')->count();
        $perempuan = $dataAnggota->where('jenis_kelamin', 'Perempuan')->count();

        $sheet->setCellValue('A' . $summaryRow, '• Total Anggota:');
        $sheet->setCellValue('C' . $summaryRow, $totalAnggota . ' anggota');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Status Aktif:');
        $sheet->setCellValue('C' . $summaryRow, $statusAktif . ' anggota');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Status Non Aktif:');
        $sheet->setCellValue('C' . $summaryRow, $statusNonAktif . ' anggota');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Jabatan Anggota:');
        $sheet->setCellValue('C' . $summaryRow, $jabatanAnggota . ' anggota');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Jabatan Pengurus:');
        $sheet->setCellValue('C' . $summaryRow, $jabatanPengurus . ' anggota');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Laki-laki:');
        $sheet->setCellValue('C' . $summaryRow, $lakiLaki . ' anggota');
        $summaryRow++;
        
        $sheet->setCellValue('A' . $summaryRow, '• Perempuan:');
        $sheet->setCellValue('C' . $summaryRow, $perempuan . ' anggota');

        $firstSummaryRow = $row + 2;
        $sheet->getStyle('A' . $firstSummaryRow . ':A' . $summaryRow)->getFont()->setSize(9);
        $sheet->getStyle('C' . $firstSummaryRow . ':C' . $summaryRow)->getFont()->setBold(true)->setSize(9);

        // ====================================
        // COLUMN WIDTH
        // ====================================
        
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(35);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(15);
        $sheet->getColumnDimension('M')->setWidth(15);

        // ====================================
        // FOOTER NOTE
        // ====================================
        
        $footerNoteRow = $summaryRow + 2;
        $sheet->mergeCells('A' . $footerNoteRow . ':M' . $footerNoteRow);
        $sheet->setCellValue('A' . $footerNoteRow, '© ' . date('Y') . ' ' . ($identitas->nama_lembaga ?? 'Koperasi') . ' - Dicetak dari Sistem Koperasi');
        $sheet->getStyle('A' . $footerNoteRow)->getFont()->setSize(8)->setItalic(true);
        $sheet->getStyle('A' . $footerNoteRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $footerNoteRow)->getFont()->getColor()->setRGB('999999');

        // ====================================
        // CATATAN KEAMANAN
        // ====================================
        
        $securityRow = $footerNoteRow + 2;
        $sheet->mergeCells('A' . $securityRow . ':M' . $securityRow);
        $sheet->setCellValue('A' . $securityRow, '⚠️ CATATAN: Data password tidak ditampilkan untuk keamanan sistem');
        $sheet->getStyle('A' . $securityRow)->getFont()->setSize(9)->setItalic(true)->setBold(true);
        $sheet->getStyle('A' . $securityRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $securityRow)->getFont()->getColor()->setRGB('DC3545');

        // ====================================
        // FREEZE PANES
        // ====================================
        
        $sheet->freezePane('A' . ($headerRow + 1));

        // ====================================
        // GENERATE FILE
        // ====================================
        
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan_Data_Anggota_' . date('d-m-Y') . '.xlsx';
        $tempFile = sys_get_temp_dir() . '/' . $fileName;

        $writer->save($tempFile);

        return [
            'path' => $tempFile,
            'filename' => $fileName
        ];
    }
}