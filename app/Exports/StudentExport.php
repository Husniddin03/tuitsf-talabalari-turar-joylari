<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

class StudentExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    protected $data;
    private $rowNumber = 0;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Sheet nomi
     */
    public function title(): string
    {
        return 'Talabalar ro\'yxati';
    }

    /**
     * Ma'lumotlarni qaytarish
     */
    public function collection()
    {
        return $this->data;
    }

    /**
     * Sarlavhalar (Guruhlanган va rangli)
     */
    public function headings(): array
    {
        return [
            // Asosiy ma'lumotlar
            '№',
            'Talaba ID',
            'F.I.SH',
            'Fakultet',
            'Guruh',
            'Telefon',
            'Tyutor',
            
            // Doimiy yashash manzili
            'Hudud',
            'Viloyat (Doimiy)',
            'Tuman (Doimiy)',
            'Ko\'cha manzili (Doimiy)',
            'Google Maps (Doimiy)',
            
            // Vaqtincha yashash manzili
            'Viloyat (Vaqtincha)',
            'Tuman (Vaqtincha)',
            'Ko\'cha manzili (Vaqtincha)',
            'Google Maps (Vaqtincha)',
            
            // Yotoqxona ma'lumotlari
            'Uy egasi',
            'Uy egasi telefoni',
            'Yotoqxona №',
            'Narx (so\'m)',
            
            // Ota-ona ma'lumotlari
            'Ota-ona',
            'Ota-ona telefoni',
            
            // Tizim ma'lumotlari
            'Ro\'yxatdan o\'tgan',
            'Yangilangan',
        ];
    }

    /**
     * Har bir qatorni formatlash
     */
    public function map($student): array
    {
        $this->rowNumber++;
        
        return [
            // Asosiy ma'lumotlar
            $this->rowNumber,
            $student->talaba_id ?? 'N/A',
            $student->fish ?? '',
            $student->fakultet ?? '',
            $student->guruh ?? '',
            $this->formatPhone($student->telefon),
            $student->tyutori ?? '',
            
            // Doimiy yashash
            $student->hudud ?? '',
            $student->doimiy_yashash_viloyati ?? '',
            $student->doimiy_yashash_tumani ?? '',
            $student->doimiy_yashash_manzili ?? '',
            $student->doimiy_yashash_manzili_urli ?? '',
            
            // Vaqtincha yashash
            $student->vaqtincha_yashash_viloyati ?? '',
            $student->vaqtincha_yashash_tumani ?? '',
            $student->vaqtincha_yashash_manzili ?? '',
            $student->vaqtincha_yashash_manzili_urli ?? '',
            
            // Yotoqxona
            $student->uy_egasi ?? '',
            $this->formatPhone($student->uy_egasi_telefoni),
            $student->yotoqxona_nomeri ?? '',
            $this->formatCurrency($student->narx),
            
            // Ota-ona
            $student->ota_ona ?? '',
            $this->formatPhone($student->ota_ona_telefoni),
            
            // Sanalar
            $student->created_at ? $student->created_at->format('d.m.Y H:i') : '',
            $student->updated_at ? $student->updated_at->format('d.m.Y H:i') : '',
        ];
    }

    /**
     * Ustunlar kengligi
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // №
            'B' => 12,  // ID
            'C' => 25,  // F.I.SH
            'D' => 20,  // Fakultet
            'E' => 12,  // Guruh
            'F' => 15,  // Telefon
            'G' => 20,  // Tyutor
            'H' => 15,  // Hudud
            'I' => 18,  // Viloyat (Doimiy)
            'J' => 18,  // Tuman (Doimiy)
            'K' => 30,  // Manzil (Doimiy)
            'L' => 35,  // URL (Doimiy)
            'M' => 18,  // Viloyat (Vaqtincha)
            'N' => 18,  // Tuman (Vaqtincha)
            'O' => 30,  // Manzil (Vaqtincha)
            'P' => 35,  // URL (Vaqtincha)
            'Q' => 20,  // Uy egasi
            'R' => 15,  // Uy egasi tel
            'S' => 12,  // Yotoqxona
            'T' => 15,  // Narx
            'U' => 20,  // Ota-ona
            'V' => 15,  // Ota-ona tel
            'W' => 18,  // Created
            'X' => 18,  // Updated
        ];
    }

    /**
     * Stillar va dizayn
     */
    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->data->count() + 1;
        
        // 1. SARLAVHA SATRI - Gradient rangli
        $sheet->getStyle('A1:X1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => '000000'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'], // To'q ko'k
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 2. ASOSIY MA'LUMOTLAR (A-G) - Och ko'k
        $sheet->getStyle("A1:G{$lastRow}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E7F3FF'],
            ],
        ]);

        // 3. DOIMIY MANZIL (H-L) - Och yashil
        $sheet->getStyle("H1:L{$lastRow}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F5E9'],
            ],
        ]);

        // 4. VAQTINCHA MANZIL (M-P) - Och sariq
        $sheet->getStyle("M1:P{$lastRow}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF9E6'],
            ],
        ]);

        // 5. YOTOQXONA (Q-T) - Och pushti
        $sheet->getStyle("Q1:T{$lastRow}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FCE4EC'],
            ],
        ]);

        // 6. OTA-ONA (U-V) - Och binafsha
        $sheet->getStyle("U1:V{$lastRow}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F3E5F5'],
            ],
        ]);

        // 7. SANALAR (W-X) - Och kulrang
        $sheet->getStyle("W1:X{$lastRow}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F5F5F5'],
            ],
        ]);

        // 8. Barcha ma'lumotlarga chegara
        $sheet->getStyle("A1:X{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // 9. Ma'lumotlar markazlash
        $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // №
        $sheet->getStyle("B2:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // ID
        $sheet->getStyle("E2:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Guruh
        $sheet->getStyle("S2:S{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Yotoqxona
        $sheet->getStyle("T2:T{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // Narx

        // 10. Sarlavha balandligi
        $sheet->getRowDimension(1)->setRowHeight(40);

        // 11. Har bir qator balandligi
        for ($i = 2; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(25);
        }

        // 12. Text wrap barcha kataklar uchun
        $sheet->getStyle("A1:X{$lastRow}")
            ->getAlignment()
            ->setWrapText(true);

        // 13. Vertikal markazlash
        $sheet->getStyle("A1:X{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        return [];
    }

    /**
     * Telefon raqamini formatlash
     */
    private function formatPhone($phone)
    {
        if (empty($phone)) return '';
        
        // +998 90 123 45 67 formatiga keltirish
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($cleaned) == 12 && substr($cleaned, 0, 3) == '998') {
            return '+' . substr($cleaned, 0, 3) . ' ' . 
                   substr($cleaned, 3, 2) . ' ' . 
                   substr($cleaned, 5, 3) . ' ' . 
                   substr($cleaned, 8, 2) . ' ' . 
                   substr($cleaned, 10, 2);
        }
        
        return $phone;
    }

    /**
     * Narxni formatlash
     */
    private function formatCurrency($amount)
    {
        if (empty($amount)) return '';
        return number_format($amount, 0, ',', ' ') . ' so\'m';
    }
}