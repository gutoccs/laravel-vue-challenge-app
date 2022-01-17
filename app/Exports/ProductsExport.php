<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\{Exportable, FromCollection, WithHeadings, WithEvents};

class ProductsExport implements FromCollection, WithHeadings, WithEvents
{

    use Exportable;

    protected $products;

    public function __construct($products = null)
    {
        $this->products = $products;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->products ?: Product::all();
    }

    public function headings(): array
    {
        return [
            ['Productos'],
            [
                'ID',
                'Nombre',
                'Descripción',
                'Precio',
                'Nombre de Imagen',
                'Fecha de Creación',
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                // No carga los estilos

                $position_last = count($this->headings()[1]);

                $column = Coordinate::stringFromColumnIndex($position_last);
                $cells = "A1:{$column}1";
                $event->sheet->mergeCells($cells);
                $event->sheet->getDelegate()->getStyle($cells)->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle($cells)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $event->sheet->getDelegate()->getStyle($cells)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            }
        ];
    }
}
