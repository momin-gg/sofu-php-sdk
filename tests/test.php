<?php

// 嗖付 SDK 测试
// 使用前请先复制 .env.example 为 .env 并填写真实配置

require __DIR__ . '/../vendor/autoload.php';

use Sofu\Pay\SofuPay;

$sdk = SofuPay::fromEnv(__DIR__ . '/../.env');

// 统一下单测试
$response = $sdk->unifiedOrder(
    'ORDER' . time(),
    0.01,
    '测试商品',
    'H5_PAY',
    'WECHAT',
    'https://your-domain.com/notify',
    ['userIp' => '127.0.0.1']
);

print_r($response);
