<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ShippingDetails;
use App\Models\AddressUser;
use App\Models\Payment;
use App\Models\CartDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    
    public function getOrderInfo(Request $request)
{
    // Kiểm tra người dùng đã đăng nhập chưa
    $user = Auth::user();
    if (!$user) {
        return response()->json(['message' => 'Không được phép. Vui lòng đăng nhập.'], 401);
    }

    // Lấy thông tin người dùng
    $userInfo = [
        'name' => $user->name,
        'email' => $user->email,
        'phone' => $user->phone,
    ];

    // Lấy địa chỉ giao hàng (chọn địa chỉ mặc định nếu có)
    $shippingAddress = AddressUser::where('user_id', $user->id)->first();  // Lấy địa chỉ đầu tiên

    if (!$shippingAddress) {
        return response()->json(['message' => 'Không tìm thấy địa chỉ giao hàng.'], 404);
    }

    // Thông tin địa chỉ giao hàng (gộp tất cả vào một trường 'address')
    $addressInfo = [
        'address' => $shippingAddress->address,  // Gộp tất cả thông tin vào một trường 'address'
    ];

    // Lấy giỏ hàng của người dùng
    $cartItems = $user->cartDetails()->with(['productVariant.product', 'productVariant.size'])->get();
    $totalCartValue = $cartItems->sum(function($item) {
        return $item->quantity * $item->productVariant->price;
    });

    // Phương thức thanh toán (chuyển tiền qua ngân hàng)
    $paymentMethod = 'Chuyển khoản ngân hàng';

    // Trả về thông tin đơn hàng
    return response()->json([
        'user_info' => $userInfo,
        'shipping_address' => $addressInfo,
        'payment_method' => $paymentMethod,
        'total_cart_value' => $totalCartValue,
    ]);
}

    
    public function updateShippingAddress(Request $request)
{
    // Kiểm tra người dùng đã đăng nhập chưa
    $user = Auth::user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized. Please login.'], 401);
    }

    // Validate dữ liệu đầu vào
    $request->validate([
        'address' => 'required|string', // Chỉ cần validate trường 'address'
        'default' => 'nullable|boolean', // Cho phép chỉ định nếu muốn làm địa chỉ mặc định
    ]);

    
    // Kiểm tra xem người dùng đã có địa chỉ mặc định chưa
    $existingAddress = AddressUser::where('user_id', $user->id)->where('default', 1)->first();

    // Nếu người dùng đã có địa chỉ mặc định, chỉ cần cập nhật
    if ($existingAddress) {
        // Cập nhật địa chỉ mặc định
        $shippingAddress = $existingAddress;
    } else {
        // Nếu không có địa chỉ mặc định, thêm mới địa chỉ
        $shippingAddress = new AddressUser();
        $shippingAddress->user_id = $user->id;
    }

    // Cập nhật trường 'address' duy nhất
    $shippingAddress->address = $request->address;

    // Nếu có chọn địa chỉ mặc định, cập nhật
    $shippingAddress->default = $request->default ?? false;  // Mặc định là false nếu không có chọn

    $shippingAddress->save();  // Lưu thay đổi

    return response()->json(['message' => 'Địa chỉ giao hàng đã được cập nhật thành công', 'địa chỉ giao hàng' => $shippingAddress]);
}


public function createOrder(Request $request)
{
    // Kiểm tra người dùng đã đăng nhập chưa
    $user = Auth::user();
    if (!$user) {
        return response()->json(['message' => 'Không được phép. Vui lòng đăng nhập.'], 401);
    }

    // Lấy giỏ hàng của người dùng
    $cartItems = CartDetail::where('user_id', $user->id)->get();
    if ($cartItems->isEmpty()) {
        return response()->json(['message' => 'Giỏ hàng của bạn hiện tại trống.'], 400);
    }

    // Lấy thông tin địa chỉ giao hàng (chọn địa chỉ mặc định nếu có)
    $shippingAddress = AddressUser::where('user_id', $user->id)->where('default', 1)->first();
    if (!$shippingAddress) {
        return response()->json(['message' => 'Không tìm thấy địa chỉ giao hàng mặc định.'], 404);
    }

    // Tính tổng tiền đơn hàng
    $totalAmount = $cartItems->sum(function($item) {
        return $item->quantity * $item->productVariant->price;
    });

    // Tạo đơn hàng mới
    $order = new Order();
    $order->user_id = $user->id;
    $order->sdt_nguoinhan = $user->phone;  // Số điện thoại người nhận
    $order->ten_nguoinhan = $user->name;   // Tên người nhận
    $order->diachi_nguoinhan = $shippingAddress->address;  // Địa chỉ người nhận
    $order->payment_method = 'Chuyển khoản ngân hàng';  // Phương thức thanh toán
    $order->payment_status = 'Chờ thanh toán';  // Đặt trạng thái thanh toán là 'Chờ thanh toán'
    $order->total = $totalAmount;  // Tổng tiền đơn hàng
    $order->status = 'Đang xử lý';  // Trạng thái đơn hàng
    $order->save();  // Lưu đơn hàng

    // Lưu chi tiết đơn hàng (sản phẩm trong giỏ hàng)
    foreach ($cartItems as $cartItem) {
        // Lưu chi tiết từng sản phẩm vào bảng OrderDetails
        $order->orderDetails()->create([
            'product_variant_id' => $cartItem->product_variant_id,
            'quantity' => $cartItem->quantity,
            'price' => $cartItem->productVariant->price,  // Cung cấp giá trị `price` từ bảng product_variants
            'subtotal' => $cartItem->quantity * $cartItem->productVariant->price,
        ]);
    }

    // Xóa sản phẩm trong giỏ hàng sau khi đặt hàng thành công
    CartDetail::where('user_id', $user->id)->delete();

    return response()->json(['message' => 'Đặt hàng thành công!', 'order' => $order]);
}

public function updateOrderStatus(Request $request, $orderId)
{
    // Kiểm tra người dùng đã đăng nhập chưa
    $user = Auth::user();
    if (!$user) {
        return response()->json(['message' => 'Không được phép. Vui lòng đăng nhập.'], 401);
    }

    // Tìm đơn hàng
    $order = Order::where('user_id', $user->id)->where('id', $orderId)->first();
    if (!$order) {
        return response()->json(['message' => 'Không tìm thấy đơn hàng'], 404);
    }

    // Cập nhật trạng thái thanh toán khi người dùng thực hiện thanh toán
    $order->payment_status = 'Đã thanh toán'; // Thay đổi trạng thái thanh toán sau khi thanh toán thành công
    $order->save();  // Lưu thay đổi

    return response()->json(['message' => 'Trạng thái thanh toán đã được cập nhật thành công', 'order' => $order]);
}



}
