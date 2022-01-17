<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\{Importable, ToModel, WithHeadingRow, WithValidation};

class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Product([
            'name'          => $row['nombre'],
            'description'   => ($row['descripcion'] == '') ? null : $row['descripcion'],
            'price'         => $row['precio'],
        ]);

    }

    public function rules() : array
    {
        return [
            'name'    => Rule::in(['max:64']),
            'price'     => Rule::in(['numeric']),
        ];
    }
}
