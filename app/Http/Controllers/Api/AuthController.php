<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
class AuthController extends Controller
{
    /**
     * Đăng ký người dùng mới
     */
    public function register(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Nếu validation thất bại
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tạo người dùng mới
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Tạo token API cho người dùng
        $token = $user->createToken('Laravel Sanctum')->plainTextToken;

        // Trả về response chứa token và thông tin người dùng
        return response()->json([
            'message' => 'Người dùng đã đăng ký thành công',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * Đăng nhập người dùng và trả về token
     */
    public function login(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        // Nếu validation thất bại
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kiểm tra thông tin đăng nhập
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Thông tin đăng nhập không hợp lệ'], 401);
        }

        // Tạo token cho người dùng
        $token = $user->createToken('Laravel Sanctum')->plainTextToken;

        // Trả về response chứa token và thông tin người dùng
        return response()->json([
            'message' => 'Đăng nhập thành công',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    /**
     * Đăng xuất người dùng và xóa token
     */
    public function logout(Request $request)
    {
        // Xóa tất cả các token của người dùng hiện tại
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Đăng xuất thành công'], 200);
    }



    public function sendResetLinkEmail(Request $request)
{
    $request->validate(['email' => 'required|email']);

    // Gửi email reset password
    $status = Password::sendResetLink(
        $request->only('email')
    );

    if ($status == Password::RESET_LINK_SENT) {
        // Trả về thông báo thành công
        return response()->json(['message' => 'Đã gửi email đổi mật khẩu!']);
    } else {
        // Trả về lỗi nếu không gửi được email
        return response()->json(['message' => 'Không gửi được email!'], 400);
    }
}

    // Reset mật khẩu với token
    public function reset(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
            'token' => 'required'
        ]);

        // Thực hiện reset mật khẩu
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        // Trả về thông báo thành công hoặc lỗi
        return $status == Password::PASSWORD_RESET
            ? response()->json(['message' => 'Mật khẩu đã được đổi thành công.'])
            : response()->json(['message' => 'Không thể thay đổi mật khẩu.'], 400);
    }
}




   
