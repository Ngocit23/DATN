<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\CartDetail;
use App\Models\AddressUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Tạo đơn hàng và khởi tạo thanh toán qua VNPAY
     */
    public function createOrder(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Vui lòng đăng nhập.'], 401);
        }

        $cartItems = CartDetail::where('user_id', $user->id)->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Giỏ hàng của bạn đang trống.'], 400);
        }

        $shippingAddress = AddressUser::where('user_id', $user->id)
            ->where('default', 1)
            ->first();

        if (!$shippingAddress) {
            return response()->json(['message' => 'Không tìm thấy địa chỉ giao hàng mặc định.'], 404);
        }

        // ✅ Tính tổng tiền đơn hàng
        $totalAmount = $cartItems->sum(fn($item) => 
            $item->quantity * $item->productVariant->price
        );

        // ✅ Tạo đơn hàng mới
        $order = new Order();
        $order->user_id = $user->id;
        $order->sdt_nguoinhan = $user->phone;
        $order->ten_nguoinhan = $user->name;
        $order->diachi_nguoinhan = $shippingAddress->address;
        $order->payment_method = 'VNPAY';
        $order->payment_status = 'Chờ thanh toán';
        $order->total = $totalAmount;
        $order->status = 'Đang xử lý';
        $order->save();

        // ✅ Lưu chi tiết đơn hàng
        foreach ($cartItems as $cartItem) {
            OrderDetail::create([
                'order_id' => $order->id,
                'product_variant_id' => $cartItem->product_variant_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->productVariant->price,
                'status' => 'pending',
            ]);
        }

        // ✅ Xóa giỏ hàng
        CartDetail::where('user_id', $user->id)->delete();

        // ✅ Gọi sang VNPAY
        return $this->createVNPAYPayment($order);
    }

    /**
     * Tạo link thanh toán VNPAY
     */
    private function createVNPAYPayment(Order $order)
{
    date_default_timezone_set('Asia/Ho_Chi_Minh'); // BẮT BUỘC

    $vnp_TmnCode = "JUA7C31M";
    $vnp_HashSecret = "DW4VAJPOGZ1LGAWA49ZFBHKI58QKSJ3J";
    $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    $vnp_ReturnUrl = route('api.vnpay.return'); // Dùng route name
    $vnp_CreateDate = date('YmdHis');
    $vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes')); // ĐÚNG: sau 15 phút

    $vnp_Params = [
        "vnp_Version"     => "2.1.0",
        "vnp_TmnCode"     => $vnp_TmnCode,
        "vnp_Amount"      => $order->total * 100,
        "vnp_Command"     => "pay",
        "vnp_CreateDate"  => $vnp_CreateDate,
        "vnp_ExpireDate"  => $vnp_ExpireDate, // BẮT BUỘC
        "vnp_CurrCode"    => "VND",
        "vnp_IpAddr"      => request()->ip(),
        "vnp_Locale"      => "vn",
        "vnp_OrderInfo"   => "Thanh toan don hang " . $order->id,
        "vnp_OrderType"   => "other",
        "vnp_TxnRef"      => $order->id,
        "vnp_ReturnUrl"   => $vnp_ReturnUrl,
    ];

    ksort($vnp_Params);

    $hashData = '';
    foreach ($vnp_Params as $key => $value) {
        $hashData .= ($hashData ? '&' : '') . $key . '=' . urlencode($value);
    }

    $vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
    $vnp_Params['vnp_SecureHash'] = $vnp_SecureHash;

    $paymentUrl = $vnp_Url . '?' . http_build_query($vnp_Params);

    return response()->json([
        'message' => 'Đặt hàng thành công!',
        'url' => $paymentUrl,
        'debug' => [
            'vnp_CreateDate' => $vnp_CreateDate,
            'vnp_ExpireDate' => $vnp_ExpireDate,
            'hashData' => $hashData,
            'secureHash' => $vnp_SecureHash
        ]
    ]);
}




    /**
     * Nhận phản hồi từ VNPAY sau khi thanh toán
     */
    public function vnpayReturn(Request $request)
{
    $vnp_HashSecret = "DW4VAJPOGZ1LGAWA49ZFBHKI58QKSJ3J";

    $inputData = $request->all();
    $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
    unset($inputData['vnp_SecureHash']);
    unset($inputData['vnp_SecureHashType']);

    ksort($inputData);
    $hashData = http_build_query($inputData, '', '&');
    $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

    $isValid = hash_equals($secureHash, $vnp_SecureHash);
    $orderId = $inputData['vnp_TxnRef'] ?? null;
    $responseCode = $inputData['vnp_ResponseCode'] ?? null;
    $transactionStatus = $inputData['vnp_TransactionStatus'] ?? null;

    $order = Order::find($orderId);

    if (!$order) {
        return response()->json([
            'success' => false,
            'message' => 'Đơn hàng không tồn tại',
        ], 404);
    }

    if ($isValid && $responseCode === '00' && $transactionStatus === '00') {
        $order->payment_status = 'Đã thanh toán';
        $order->status = 'Đã xác nhận';
        $order->vnpay_transaction_no = $inputData['vnp_TransactionNo'] ?? null;
        $order->vnpay_bank_code = $inputData['vnp_BankCode'] ?? null;
        $order->vnpay_pay_date = $inputData['vnp_PayDate'] ?? null;
        $order->save();

        // Cập nhật chi tiết đơn hàng
        OrderDetail::where('order_id', $order->id)
            ->update(['status' => 'confirmed']);

        return response()->json([
            'success' => true,
            'message' => 'Thanh toán thành công!',
            'order_id' => $order->id,
            'amount' => ($inputData['vnp_Amount'] ?? 0) / 100,
            'transaction_no' => $inputData['vnp_TransactionNo'] ?? null,
            'bank' => $inputData['vnp_BankCode'] ?? null,
            'pay_date' => $inputData['vnp_PayDate'] ?? null,
        ]);
    } else {
        $order->payment_status = 'Thanh toán thất bại';
        $order->status = 'Đã hủy';
        $order->save();

        return response()->json([
            'success' => false,
            'message' => 'Thanh toán thất bại',
            'error_code' => $responseCode,
            'order_id' => $order->id,
        ], 400);
    }
}

}
