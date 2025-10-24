<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentExport implements FromArray, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        // Collection ni array ga aylantirish
        return $this->data->toArray();
    }

    public function headings(): array
    {
        if (empty($this->data)) {
            return [];
        }
        
        // Birinchi elementdan kalitlarni olish
        $first = $this->data->first();
        return array_keys($first->toArray());
    }
}