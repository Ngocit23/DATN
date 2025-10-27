<?php

namespace App\Http\Controllers\Api;

use App\Models\CartDetail;
use App\Models\ProductVariant;
use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class CartController extends Controller
{
    // Thêm sản phẩm vào giỏ hàng
    public function addToCart(Request $request)
    {
        // Kiểm tra nếu người dùng chưa được xác thực
        if (!$request->user()) {
            return response()->json(['message' => 'Bạn cần phải đăng nhập'], 401);
        }

        // Kiểm tra biến thể sản phẩm có tồn tại không
        $variant = ProductVariant::findOrFail($request->product_variant_id);

        // Tính thành tiền (subtotal)
        $subtotal = $variant->price * $request->quantity;

        // Kiểm tra nếu sản phẩm đã có trong giỏ hàng thì chỉ cập nhật số lượng
        $cartItem = CartDetail::updateOrCreate(
            ['user_id' => $request->user()->id, 'product_variant_id' => $request->product_variant_id],
            ['quantity' => $request->quantity, 'subtotal' => $subtotal]
        );

        return response()->json([
            'message' => 'Sản phẩm đã được thêm vào giỏ hàng',
            'cart_item' => $cartItem
        ]);
    }


    // Cập nhật giỏ hàng
    public function updateCart(Request $request, $id)
    {
        // Lấy sản phẩm trong giỏ hàng
        $cartItem = CartDetail::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Sản phẩm không tồn tại trong giỏ hàng'], 404);
        }

        // Cập nhật số lượng và tính lại subtotal
        $variant = $cartItem->productVariant; // Kiểm tra nếu quan hệ đã được định nghĩa trong mô hình CartDetail
        $subtotal = $variant->price * $request->quantity;

        $cartItem->update([
            'quantity' => $request->quantity,
            'subtotal' => $subtotal
        ]);

        return response()->json(['message' => 'Giỏ hàng đã được cập nhật', 'cart_item' => $cartItem]);
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function removeFromCart(Request $request, $id)
    {
        // Tìm sản phẩm trong giỏ hàng
        $cartItem = CartDetail::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Sản phẩm không tồn tại trong giỏ hàng'], 404);
        }

        // Xóa sản phẩm khỏi giỏ hàng
        $cartItem->delete();

        return response()->json(['message' => 'Sản phẩm đã được xóa khỏi giỏ hàng']);
    }

    // Áp dụng mã giảm giá vào giỏ hàng
    public function applyVoucher(Request $request)
    {
        // Kiểm tra mã giảm giá hợp lệ
        $voucher = Voucher::where('code', $request->voucher_code)->first();

        if (!$voucher) {
            return response()->json(['message' => 'Mã giảm giá không hợp lệ'], 400);
        }

        // Kiểm tra trạng thái của voucher (phải là 'active')
        if ($voucher->status != 'active') {
            return response()->json(['message' => 'Mã giảm giá không còn hiệu lực'], 400);
        }

        // Kiểm tra thời gian sử dụng của voucher
        $currentDate = Carbon::now();
        if ($voucher->start_date > $currentDate || $voucher->end_date < $currentDate) {
            return response()->json(['message' => 'Mã giảm giá đã hết hạn'], 400);
        }

        // Kiểm tra giá trị đơn hàng tối thiểu
        $total = $request->total;
        if ($total < $voucher->min_order_value) {
            return response()->json(['message' => 'Giỏ hàng không đủ điều kiện để sử dụng mã giảm giá'], 400);
        }

        // Tính toán giảm giá
        $discountAmount = 0;
        if ($voucher->discount_type == 'percentage') {
            // Giảm theo phần trăm
            $discountAmount = ($voucher->discount_value / 100) * $total;
        } elseif ($voucher->discount_type == 'fixed') {
            // Giảm theo giá trị cố định
            $discountAmount = $voucher->discount_value;
        }

        // Kiểm tra mức giảm giá tối đa
        if ($voucher->max_discount_value && $discountAmount > $voucher->max_discount_value) {
            $discountAmount = $voucher->max_discount_value;
        }

        // Tính lại tổng giỏ hàng sau khi áp dụng voucher
        $newTotal = $total - $discountAmount;

        return response()->json([
            'message' => 'Voucher đã được áp dụng',
            'voucher_code' => $voucher->code,
            'discount_amount' => $discountAmount,
            'new_total' => $newTotal
        ]);
    }

    // Thực hiện thanh toán
    public function checkout(Request $request)
    {
        $cartItems = CartDetail::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->get();

        // Tính toán tổng giỏ hàng
        $total = $cartItems->sum('subtotal');
        
        // Tính phí vận chuyển và các phí khác
        $shippingFee = 30; // Giả sử phí vận chuyển là 30

        // Tổng giỏ hàng sau phí vận chuyển
        $totalAmount = $total + $shippingFee;

        // Trả về thông tin thanh toán
        return response()->json([
            'total' => $total,
            'shipping_fee' => $shippingFee,
            'total_amount' => $totalAmount,
            'message' => 'Giỏ hàng sẵn sàng để thanh toán'
        ]);
    }
}
