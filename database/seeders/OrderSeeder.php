<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\AddressUser;
use App\Models\CartDetail;
use App\Models\ProductVariant;

class OrderSeeder extends Seeder
{
    public function run()
    {
        // Lấy người dùng (giả sử bạn đã có ít nhất một người dùng trong bảng users)
        $user = User::first();  // Lấy người dùng đầu tiên trong bảng users

        // Lấy số điện thoại của người dùng
        $phoneNumber = $user->phone;  // Lấy số điện thoại từ bảng users

        if (!$phoneNumber) {
            $this->command->info('Số điện thoại người dùng không có.');
            return;
        }

        // Lấy địa chỉ giao hàng mặc định của người dùng (giả sử bạn đã có địa chỉ giao hàng)
        $shippingAddress = AddressUser::where('user_id', $user->id)->where('default', 1)->first();

        if (!$shippingAddress) {
            // Nếu không có địa chỉ giao hàng mặc định, tạo một địa chỉ mới cho người dùng
            $shippingAddress = AddressUser::create([
                'user_id' => $user->id,
                'address' => '123 Main St, Hanoi, Hai Ba Trung, Phuong Mai',
                'default' => true
            ]);
        }

        // Lấy sản phẩm từ bảng `product_variants` (giả sử bạn có sản phẩm trong bảng này)
        $productVariant = ProductVariant::first();  // Lấy sản phẩm đầu tiên trong bảng product_variants

        if (!$productVariant) {
            $this->command->info('No product variants found.');
            return;
        }

        // Thêm sản phẩm vào giỏ hàng
        $cartItem = new CartDetail();
        $cartItem->user_id = $user->id;
        $cartItem->product_variant_id = $productVariant->id;
        $cartItem->quantity = 2;  // Thêm 2 sản phẩm
        $cartItem->subtotal = $productVariant->price * 2;  // Tính giá trị subtotal
        $cartItem->save();  // Lưu sản phẩm vào giỏ hàng

        // Lấy giỏ hàng của người dùng (mới thêm sản phẩm vào giỏ hàng)
        $cartItems = CartDetail::where('user_id', $user->id)->get();

        // Tính tổng tiền đơn hàng từ giỏ hàng
        $totalAmount = $cartItems->sum(function($item) {
            return $item->quantity * $item->productVariant->price;
        });

        // Tạo đơn hàng mới
        $order = Order::create([
            'user_id' => $user->id,
            'sdt_nguoinhan' => $phoneNumber,  // Đảm bảo rằng sdt_nguoinhan không bị null
            'ten_nguoinhan' => $user->name,   // Tên người nhận
            'diachi_nguoinhan' => $shippingAddress->address,  // Địa chỉ người nhận
            'payment_method' => 'Chuyển khoản ngân hàng',  // Phương thức thanh toán
            'payment_status' => 'Đã thanh toán',  // Trạng thái thanh toán
            'total' => $totalAmount,  // Tổng tiền đơn hàng
            'status' => 'Đang xử lý',  // Trạng thái đơn hàng
        ]);

        // Lưu chi tiết đơn hàng (sản phẩm trong giỏ hàng)
        foreach ($cartItems as $cartItem) {
            // Cung cấp giá trị cho cột `price` từ sản phẩm trong bảng `product_variants`
            $order->orderDetails()->create([
                'product_variant_id' => $cartItem->product_variant_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->productVariant->price,  // Cung cấp giá trị price từ bảng product_variants
                'status' => 'pending',  // Đặt giá trị mặc định là 'pending' cho status
            ]);
        }

        // Xóa sản phẩm trong giỏ hàng sau khi đặt hàng thành công
        CartDetail::where('user_id', $user->id)->delete();

        $this->command->info('Order created successfully!');
    }
}
