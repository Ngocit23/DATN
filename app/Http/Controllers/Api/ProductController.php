<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ProductController extends Controller
{
    // API cho trang sản phẩm
    public function index(Request $request)
    {
        // === PHẦN BÊN TRÁI: FILTER ===
        $filters = [
            'genders' => ['Nam', 'Nữ', 'Unisex'],
            'featured' => [
                ['id' => 'hot', 'name' => 'Nổi bật'],
                ['id' => 'sale', 'name' => 'Đang giảm giá'],
                ['id' => 'new', 'name' => 'Mới']
            ],
            'brands' => Brand::select('id', 'name')->get(),
            'accessories' => Category::where('id', 3)->select('id', 'name')->get(),
            'colors' => Color::whereHas('productVariants')->select('id', 'type')->get(),
        ];

        // === PHẦN BÊN PHẢI: DANH SÁCH SẢN PHẨM ===
        $query = Product::with(['variants.color', 'brand', 'images'])
            ->where('status', 1);

        // --- Các bộ lọc ---
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('featured')) {
            switch ($request->featured) {
                case 'hot':
                    $query->where('status', 1);                    break;
                case 'sale':
                    $query->whereHas('variants', function ($q) {
                        $q->whereNotNull('price_sale')
                          ->whereColumn('price_sale', '<', 'price');
                    });
                    break;
                case 'new':
                    $query->where('created_at', '>=', now()->subDays(30));
                    break;
            }
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('is_accessory') && $request->is_accessory == true) {
            $query->where('category_id', 3); // Phụ kiện
        }

        if ($request->filled('color_id')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('color_id', $request->color_id);
            });
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // --- Lấy sản phẩm có phân trang ---
        $products = $query->paginate(10);

        // --- Format sản phẩm ---
        $productsFormatted = $products->through(function ($product) {
            $variant = $product->variants->first();

            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'brand' => [
                    'id' => $product->brand->id ?? null,
                    'name' => $product->brand->name ?? null,
                ],
                'price' => $variant?->price,
                'price_sale' => $variant?->price_sale,
                
                'image' => $product->images->first()
                ? asset('storage/' . $product->images->first()->image_url)
                : null,
                //'images' => $product->images->map(fn($img) => asset('storage/' . $img->image_url)), lấy all ảnh
               
               
                'colors' => $product->variants
                ->pluck('color.type')// lấy tên màu từ quan hệ
                    ->filter()
                    ->unique()
                    ->values(),
            ];
        });

        // --- Trả về JSON ---
        return response()->json([
            'filters' => $filters,
            'products' => [
                'current_page' => $products->currentPage(),
                'data' => $productsFormatted,
                'first_page_url' => $products->url(1),
                'from' => $products->firstItem(),
                'last_page' => $products->lastPage(),
                'last_page_url' => $products->url($products->lastPage()),
                'next_page_url' => $products->nextPageUrl(),
                'path' => $products->path(),
                'per_page' => $products->perPage(),
                'prev_page_url' => $products->previousPageUrl(),
                'to' => $products->lastItem(),
                'total' => $products->total(),
            ]
        ]);
    }
}
