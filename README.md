# Sofu YOP PHP SDK

一个用于对接嗖付 YOP 支付网关的轻量级 PHP SDK，封装了参数管理、签名生成、HTTP 请求发送以及回调报文解密等常用能力。

## 安装

项目已支持通过 Composer 自动加载：

```bash
composer require yourname/sofu-yop-sdk
```

运行环境要求：

- PHP >= 7.2
- 已启用扩展：`ext-curl`、`ext-json`、`ext-openssl`

## 快速开始

```php
use Sofu\Yop\SofuYopClient;

$appKey     = 'your-app-key';
$privateKey = 'your-private-key';

// 可选：根据环境替换为正式网关地址
$client = new SofuYopClient($appKey, $privateKey, 'https://developer.sofubao.com');

// 设置业务参数（示例为创建支付订单）
$client->addParam('order_no', 'SF' . time());
$client->addParam('amount', 100);
$client->addParam('notify_url', 'https://yourdomain.com/notify');

// 发起请求
$result = $client->post('/api/pay/create');

// 根据返回结果进行业务处理
var_dump($result);
```

## 回调解密示例

嗖付回调报文中通常会包含 `encrypted_data` 字段，用于承载加密后的业务数据。可以使用 `SofuYopClient::decryptPayload` 进行解密：

```php
use Sofu\Yop\SofuYopClient;

// 回调时从 php://input 读取原始报文
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!isset($data['encrypted_data'])) {
    // 根据业务需要记录日志或返回错误
    return;
}

$encrypted  = $data['encrypted_data'];
$decryptKey = 'your-callback-decrypt-key';

$client = new SofuYopClient($appKey, $privateKey);

$payload = $client->decryptPayload($encrypted, $decryptKey);

// $payload 即为解密后的数组，根据其中字段更新订单状态等
```

## 本地联调与示例脚本

项目内提供了一个简单的本地联调示例脚本：`tests/test.php`，主要用于：

- 演示如何构造支付请求并调用支付接口；
- 演示如何模拟回调并使用 `decryptPayload` 解密 `encrypted_data`。

使用方式：

1. 执行 `composer install` 安装依赖；
2. 打开 `tests/test.php`，将其中的 `appKey`、`privateKey`、`$decryptKey` 替换为真实值；
3. 在项目根目录执行：

   ```bash
   php tests/test.php
   ```

   根据需要在脚本中调用 `pay($client)` 或 `callback($client, $decryptKey)` 进行调试。

## 测试与扩展

- 当前示例脚本适合作为联调/调试使用；
- 如需更完善的自动化回归测试，可以在此基础上引入 PHPUnit，为签名生成、回调解密等核心逻辑编写单元测试。
