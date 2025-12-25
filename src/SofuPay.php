<?php

namespace Sofu\Pay;

use Sofu\Pay\Lib\HttpClient;
use Sofu\Pay\Lib\Utils;
use Sofu\Pay\Lib\Api;

/**
 * 嗖付 SDK
 */
class SofuPay
{
    use Api;

    private $client;
    private $decryptKey;

    /**
     * 构造函数 - 自动加载 .env 配置
     */
    public function __construct()
    {
        $envPath = $this->findEnvFile();
        if ($envPath) {
            Utils::loadEnv($envPath);
        }

        $this->decryptKey = Utils::env('SOFU_DECRYPT_KEY', '');
        $this->client = new HttpClient([
            'merchant_no' => Utils::env('SOFU_MERCHANT_NO'),
            'app_key'     => Utils::env('SOFU_APP_KEY'),
            'private_key' => Utils::env('SOFU_PRIVATE_KEY'),
            'endpoint'    => Utils::env('SOFU_ENDPOINT', 'https://developer.sofubao.com'),
            'timeout'     => 30,
        ]);
    }

    private function findEnvFile()
    {
        $paths = [
            dirname(__DIR__) . '/.env',
            getcwd() . '/.env',
            dirname(getcwd()) . '/.env',
        ];
        foreach ($paths as $path) {
            if (file_exists($path)) return $path;
        }
        return null;
    }
}
