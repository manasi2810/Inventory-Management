<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductsImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $header = $rows->shift()->map(fn($h) => trim($h));

        foreach ($rows as $row) {

            if (empty(array_filter($row->toArray()))) {
                continue;
            }

            $data = array_combine($header->toArray(), $row->toArray());

            Product::create([
                'name' => $data['name'] ?? null,
                'sku' => $data['sku'] ?? null,

                'category_id' => Category::where('name', $data['category'] ?? null)->value('id'),

                'description' => $data['description'] ?? null,

                'price' => $data['price'] ?? 0,

                'opening_stock' => $data['opening_stock'] ?? 0,
                'pack_size' => $data['pack_size'] ?? null,
                'moq' => $data['moq'] ?? 0,
                'uom' => $data['uom'] ?? null,

                'status' => $data['status'] ?? 'active',
                'feature_product' => $data['feature_product'] ?? 0,

                'page_title' => $data['page_title'] ?? null,
                'alt_text' => $data['alt_text'] ?? null,
                'meta_keywords' => $data['meta_keywords'] ?? null,
            ]);
        }
    }
}