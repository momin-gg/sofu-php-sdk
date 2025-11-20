<?php

// 本文件为本地联调/示例脚本，用于演示如何调用 SofuYopClient 发起支付
// 以及如何处理回调中的加密数据。运行前请先在下方配置真实的 appKey、私钥等信息。

require __DIR__ . '/../vendor/autoload.php';

use Sofu\Yop\SofuYopClient;

/**
 * ------------------------------------------------------------
 * 配置
 * ------------------------------------------------------------
 */
$appKey     = "your-app-key";
$privateKey = "your-private-key";
$decryptKey = "a5a4d83cb5bbafdf34b4801df3b4a398677660a395bdc231c90f955dd598f508"; // 回调解密 key

$client = new SofuYopClient($appKey, $privateKey);


/**
 * ------------------------------------------------------------
 * 1. 支付方法
 * ------------------------------------------------------------
 *
 * 使用 SofuYopClient 构造一笔示例支付请求并调用支付接口。
 * 实际项目中，你需要根据业务填写真实的订单号、金额和回调地址等参数。
 */
function pay(SofuYopClient $client)
{
    echo "=== 发起支付请求 ===\n";

    $client->addParam("order_no", "SF" . time());
    $client->addParam("amount", 100);
    $client->addParam("notify_url", "https://yourdomain.com/notify");

    $result = $client->post("/api/pay/create");

    echo "支付结果：\n";
    print_r($result);
}


/**
 * ------------------------------------------------------------
 * 2. 回调处理方法（直接使用 decryptPayload）
 * ------------------------------------------------------------
 *
 * 模拟支付回调处理流程，演示如何从回调报文中取出 encrypted_data
 * 并使用 SDK 提供的 decryptPayload 进行解密。
 * 实际项目中应从 php://input 读取真实回调报文，并根据解密结果更新订单状态等。
 */
function callback(SofuYopClient $client, $decryptKey)
{
    echo "=== 模拟支付回调处理 ===\n";

    /**
     * 真实情况应该用：
     * $raw = file_get_contents("php://input");
     *
     * 这里为了演示，用模拟数据
     */

    $raw = json_encode([
        "encrypted_data" => "这里填写嗖付回调中的 encrypted_data Base64 字符串"
    ]);

    $data = json_decode($raw, true);

    if (!isset($data['encrypted_data'])) {
        echo "错误：回调缺少 encrypted_data 字段\n";
        return;
    }

    $encrypted = $data['encrypted_data'];

    // 使用 SDK 内置 decryptPayload 解密
    $callbackData = $client->decryptPayload($encrypted, $decryptKey);

    echo "解密后的回调内容：\n";
    print_r($callbackData);

    // 这里你可以继续写业务逻辑，比如更新订单状态等等
    echo "\n回调处理完成。\n";
}


