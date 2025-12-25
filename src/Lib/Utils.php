<?php

namespace Sofu\Pay\Lib;

/**
 * 工具类
 */
class Utils
{
    /**
     * 生成签名
     */
    public static function sign(array $params, $privateKey)
    {
        $params = array_filter($params, function ($v) {
            return $v !== null && $v !== '';
        });
        ksort($params);
        return hash_hmac('sha256', http_build_query($params), $privateKey);
    }

    /**
     * 解密回调数据
     */
    public static function decrypt($data, $key)
    {
        $iv = substr(md5($key), 0, 16);
        $decrypted = openssl_decrypt(base64_decode($data), 'AES-256-CBC', $key, 0, $iv);
        return $decrypted ? json_decode($decrypted, true) : null;
    }

    /**
     * 加载 .env 文件
     */
    public static function loadEnv($path)
    {
        if (!file_exists($path)) {
            return;
        }
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') {
                continue;
            }
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                if (!getenv($key)) {
                    putenv("$key=$value");
                }
            }
        }
    }

    /**
     * 获取环境变量
     */
    public static function env($key, $default = '')
    {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}
