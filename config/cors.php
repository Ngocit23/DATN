<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel CORS Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for CORS. By default, Laravel
    | allows all origins, methods, and headers for your APIs.
    |
    */

    'paths' => ['api/*'],  // Chỉ áp dụng CORS cho các route API

    'allowed_methods' => ['*'],  // Cho phép tất cả các phương thức HTTP

    'allowed_origins' => ['http://localhost', 'https://yourfrontenddomain.com'],  // Cấu hình cho phép các domain cụ thể

    'allowed_headers' => ['*'],  // Cho phép tất cả các headers, có thể điều chỉnh nếu cần

    'exposed_headers' => [],  // Nếu bạn muốn cho phép client truy cập thêm các headers, có thể thêm vào đây

    'max_age' => 86400,  // Thời gian cache preflight request là 1 ngày (86400 giây)

    'supports_credentials' => true,  // Hỗ trợ cookie hoặc credentials nếu frontend yêu cầu token hoặc cookie trong header
];
