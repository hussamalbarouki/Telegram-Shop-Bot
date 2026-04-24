<?php

namespace App\Services;

use App\Models\Product;

class ProductCatalogService
{
    public function featured(int $limit = 10)
    {
        return Product::query()->where('is_featured', true)->where('status', 'active')->orderBy('sort_order')->limit($limit)->get();
    }
}
