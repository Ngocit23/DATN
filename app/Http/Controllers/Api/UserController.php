<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Trang chủ - Home API
     */
    public function home()
{
    $categories = Category ::select('id', 'name','description', 'slug')->get();

    // Lấy sản phẩm status = 1 (đang hoạt động)
    $Products_hot = Product::with('variants', 'brand') // Thêm brand vào
    ->where('status', 1)
    ->select('id', 'name', 'slug', 'type', 'brand_id', 'category_id', 'status', 'created_at', 'updated_at')
    ->limit(8)
    ->get();



    $Products_sale = Product::with('variants', 'brand') // Thêm brand vào
    ->whereHas('variants', function (Builder $query) {
        $query->whereNotNull('price_sale')
              ->where('price_sale', '<', \DB::raw('price'));
    })
    ->with(['variants' => function ($query) {
        $query->whereNotNull('price_sale')->orderBy('price_sale', 'asc');
    }, 'brand']) // Thêm brand vào đây luôn
    ->limit(8)
    ->get();


    return response()->json([
        'categories' => $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'image' => asset('storage/' . $category->image),
            ];
        }),

        'products_hot' => $Products_hot->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->variants->first()->price ?? null,
                'image' => $product->images->first()
                ? asset('storage/' . $product->images->first()->image_url)
                : null,
                'slug' => $product->slug,
                'brand' => [
                    'id' => $product->brand->id ?? null,
                    'name' => $product->brand->name ?? null,
                ],
            ];
        }),
        

        'products_sale' => $Products_sale->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->variants->first()->price ?? null,
                'price_sale' => $product->variants->first()->price_sale ?? null,
                'image' => $product->images->first()
                ? asset('storage/' . $product->images->first()->image_url)
                : null,
                'slug' => $product->slug,
                'brand' => [
                    'id' => $product->brand->id ?? null,
                    'name' => $product->brand->name ?? null,
                ],
            ];
        }),
        
        
    ]);
}
    










     public function index()
    {
    }
    public function store(Request $request)
    {
    }
    public function show(string $id)
    {
    }
    public function update(Request $request, string $id)
    {
    }

    public function destroy(string $id)
    {
    }
}
