# Sofu PHP SDK

嗖付聚合支付 PHP SDK API 调用。

## 安装

```bash
composer require sofu/php-sdk
```

**要求：** PHP >= 7.2

## 快速开始

**1. 复制配置文件**

```bash
cp .env.example .env
```

**2. 编辑 `.env` 填写配置**

```env
SOFU_MERCHANT_NO=你的商户号
SOFU_APP_KEY=你的AppKey
SOFU_PRIVATE_KEY=你的私钥
SOFU_DECRYPT_KEY=回调解密密钥
SOFU_ENDPOINT=https://developer.sofubao.com
```

**3. 调用 SDK**

```php
use Sofu\Pay\SofuPay;

$sdk = new SofuPay();
$response = $sdk->queryBalance();
```

## API 方法列表

### 1. 聚合支付统一下单

```php
$response = $sdk->unifiedOrder(
    'ORDER' . time(),           // 订单号
    100.00,                     // 金额
    '测试商品',                  // 商品名
    'H5_PAY',                   // 支付方式：NATIVE|H5_PAY|MINI_PROGRAM
    'WECHAT',                   // 渠道：WECHAT|ALIPAY|UNIONPAY
    'https://your-domain.com/notify',  // 回调地址
    ['userIp' => '127.0.0.1', 'returnUrl' => 'https://your-domain.com/return']  // 可选参数
);
```

### 2. 订单查询

```php
$response = $sdk->queryOrder('ORDER20231119001');
```

### 3. 申请退款

```php
$response = $sdk->refund('ORDER20231119001', 50.00, '用户申请退款');
```

### 4. 退款查询

```php
$response = $sdk->queryRefund();
```

### 5. 账户余额查询

```php
$response = $sdk->queryBalance();
```

### 6. 待结算查询

```php
$response = $sdk->queryPendingSettlement();
```

## 回调解密

```php
$sdk->setDecryptKey('your-callback-decrypt-key');

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

$payload = $sdk->decryptCallback($data['encrypted_data']);
```

## 响应处理

所有接口返回数组格式，`code === 6000` 表示成功：

```php
if ($response['code'] === 6000) {
    $result = $response['result'];
    // 处理业务逻辑
} else {
    echo "错误：" . $response['message'];
}
```

## 文件结构

```
.env.example          # 配置模板
src/
├── SofuPay.php       # SDK 入口（use Api）
└── Lib/
    ├── Api.php         # API 方法 Trait
    ├── HttpClient.php  # HTTP 请求
    └── Utils.php       # 工具类
```

## 示例脚本

查看 `tests/test.php` 获取完整使用示例。
