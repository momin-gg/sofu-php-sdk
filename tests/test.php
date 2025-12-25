<?php
/**
 * Sofu PHP SDK 测试
 * 
 * 使用前请复制 .env.example 为 .env 并填写配置
 */

require __DIR__ . '/../vendor/autoload.php';

use Sofu\Pay\SofuPay;

$sdk = new SofuPay();

$response = $sdk->unifiedOrder(
    'SF' . date('YmdHis'),
    0.01,
    '测试商品',
    'H5_PAY',
    'WECHAT',
    'https://example.com/notify'
);

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
