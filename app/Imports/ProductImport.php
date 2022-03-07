<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Product([
            $product->product_name  = $row['product_name'],
            $product->video         = $row['video'],
            $product->stock         = $row['stock'],
            $product->price         = $row['price'],
            $product->shop_id       = $row['shop_id'],
        ]);
    }
}
