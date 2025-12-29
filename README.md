# Sofu PHP SDK

> 嗖付聚合支付 PHP SDK

## 安装

```bash
composer require moming/sofu-php-sdk
```

## 配置

复制 `.env.example` 为 `.env`，填写配置：

```env
SOFU_MERCHANT_NO=商户号
SOFU_APP_KEY=AppKey
SOFU_PRIVATE_KEY=私钥
SOFU_DECRYPT_KEY=回调解密密钥
SOFU_ENDPOINT=https://developer.sofubao.com
```

## 使用

```php
use Sofu\Pay\SofuPay;

$sdk = new SofuPay();

// H5 支付
$response = $sdk->unifiedOrder(
    'ORDER' . time(),                  // 订单号
    100.00,                            // 金额（元）
    '商品名称',                         // 商品名
    'H5_PAY',                          // 支付方式: NATIVE | H5_PAY | MINI_PROGRAM
    'WECHAT',                          // 渠道: WECHAT | ALIPAY | UNIONPAY
    'https://your-domain.com/notify',  // 回调地址
    '127.0.0.1',                       // 用户IP
    '2024-12-29'                        //订单截止时间
    'https://your-domain.com/return'   // 跳转地址（可选）
);

// 订单查询
$response = $sdk->queryOrder('ORDER20231119001');

// 退款
$response = $sdk->refund('ORDER20231119001', 50.00, '用户申请退款');

// 余额查询
$response = $sdk->queryBalance();

// 待结算查询
$response = $sdk->queryPendingSettlement();

// 回调解密
$sdk->setDecryptKey('your-callback-decrypt-key');
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);
$payload = $sdk->decryptCallback($data['encrypted_data']);
```

## API

| 方法                       | 说明       |
| -------------------------- | ---------- |
| `unifiedOrder()`           | 统一下单   |
| `queryOrder()`             | 订单查询   |
| `refund()`                 | 申请退款   |
| `queryRefund()`            | 退款查询   |
| `queryBalance()`           | 余额查询   |
| `queryPendingSettlement()` | 待结算查询 |
| `decryptCallback()`        | 回调解密   |

## 响应

```php
if ($response['code'] === 6000) {
    // 成功
} else {
    echo $response['message'];
}
```

## 结构

```
src/
├── SofuPay.php         # 入口
└── Lib/
    ├── Api.php         # API Trait
    ├── HttpClient.php  # HTTP
    └── Utils.php       # 工具
```
