<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartDetail;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Thêm sản phẩm vào giỏ hàng
    public function addToCart(Request $request)
{
    // Kiểm tra người dùng đã đăng nhập chưa
    $user = Auth::user();

    // Nếu người dùng không đăng nhập, trả về lỗi Unauthorized
    if (!$user) {
        return response()->json(['message' => 'Unauthorized. Please login.'], 401);
    }

    // Validate dữ liệu đầu vào
    $request->validate([
        'product_variant_id' => 'required|exists:product_variants,id',
        'quantity' => 'required|integer|min:1',
    ]);

    // Kiểm tra sản phẩm có trong giỏ hàng chưa
    $existingCartItem = CartDetail::where('user_id', $user->id)
                                  ->where('product_variant_id', $request->product_variant_id)
                                  ->first();

    if ($existingCartItem) {
        return response()->json(['message' => 'Sản phẩm đã có trong giỏ hàng'], 400);
    }

    // Lấy thông tin sản phẩm variant
    $productVariant = ProductVariant::find($request->product_variant_id);
    if (!$productVariant) {
        return response()->json(['message' => 'Biến thể sản phẩm không tồn tại'], 404);
    }

    // Kiểm tra số lượng tồn kho
    if ($request->quantity > $productVariant->quantity) {
        return response()->json(['message' => 'Số lượng yêu cầu vượt quá số lượng tồn kho'], 400);
    }

    // Thêm sản phẩm vào giỏ hàng
    $cartItem = CartDetail::create([
        'user_id' => $user->id,  // Dùng user_id vì đã đăng nhập
        'product_variant_id' => $request->product_variant_id,
        'quantity' => $request->quantity,
        'subtotal' => $request->quantity * $productVariant->price,
    ]);

    return response()->json([
        'message' => 'Sản phẩm đã được thêm vào giỏ hàng',
        'cart_item' => $cartItem
    ], 200);
}






    // Lấy giỏ hàng của người dùng
    public function getCart(Request $request)
{
    // Kiểm tra người dùng đã đăng nhập chưa
    $user = Auth::user();

    // Nếu người dùng chưa đăng nhập, trả về lỗi Unauthorized
    if (!$user) {
        return response()->json(['message' => 'Unauthorized. Please login.'], 401);
    }

    // Lấy giỏ hàng của người dùng với thông tin về sản phẩm và kích cỡ
    $cartItems = CartDetail::with(['productVariant.size'])  // Eager load size thông qua product_variant
        ->where('user_id', $user->id)  // Lọc giỏ hàng theo user_id
        ->get();

    return response()->json($cartItems);
}



    // Cập nhật giỏ hàng (số lượng, size)
    public function updateCart(Request $request)
{
    // Kiểm tra người dùng đã đăng nhập chưa
    $user = Auth::user();

    // Nếu người dùng chưa đăng nhập, trả về lỗi Unauthorized
    if (!$user) {
        return response()->json(['message' => 'Unauthorized. Please login.'], 401);
    }

    // Validate dữ liệu đầu vào
    $request->validate([
        'product_variant_id' => 'required|exists:product_variants,id',
        'quantity' => 'required|integer|min:1',
        'size_id' => 'nullable|exists:sizes,id',
    ]);

    // Lấy sản phẩm variant trong giỏ hàng
    $cartItem = CartDetail::where('user_id', $user->id)
                          ->where('product_variant_id', $request->product_variant_id)
                          ->first();

    if (!$cartItem) {
        return response()->json(['message' => 'Sản phẩm không có trong giỏ hàng'], 404);
    }

    // Kiểm tra số lượng hợp lệ
    $productVariant = ProductVariant::find($request->product_variant_id);
    if ($request->quantity > $productVariant->quantity) {
        return response()->json(['message' => 'Số lượng yêu cầu vượt quá số lượng tồn kho'], 400);
    }

    // Cập nhật giỏ hàng
    $cartItem->update([
        'quantity' => $request->quantity,
        'subtotal' => $request->quantity * $productVariant->price,
    ]);

    // Cập nhật size nếu có thay đổi
    if ($request->filled('size_id')) {
        $cartItem->size_id = $request->size_id;
        $cartItem->save();
    }

    return response()->json(['message' => 'Giỏ hàng đã được cập nhật']);
}


    // Xóa sản phẩm khỏi giỏ hàng
    public function removeFromCart(Request $request)
{
    // Validate dữ liệu đầu vào (product_variant_id là bắt buộc)
    $request->validate([
        'product_variant_id' => 'required|exists:product_variants,id',
    ]);

    // Lấy người dùng từ Auth
    $user = Auth::user();
    
    // Nếu người dùng không đăng nhập, trả về lỗi Unauthorized
    if (!$user) {
        return response()->json(['message' => 'Unauthorized. Please login.'], 401);
    }

    // Tìm sản phẩm trong giỏ hàng của người dùng
    $cartItem = CartDetail::where('user_id', $user->id)
                          ->where('product_variant_id', $request->product_variant_id)
                          ->first();

    if (!$cartItem) {
        return response()->json(['message' => 'Sản phẩm không có trong giỏ hàng'], 404);
    }

    // Xóa sản phẩm khỏi giỏ hàng
    $cartItem->delete();

    return response()->json(['message' => 'Sản phẩm đã được xóa khỏi giỏ hàng']);
}

}
