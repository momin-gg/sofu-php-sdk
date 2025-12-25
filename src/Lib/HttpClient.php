<?php

namespace Sofu\Pay\Lib;

/**
 * HTTP 请求客户端
 */
class HttpClient
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * POST 请求 (JSON)
     */
    public function post($path, array $params = [])
    {
        return $this->request('POST', $path, $params, 'json');
    }

    /**
     * GET 请求
     */
    public function get($path, array $params = [])
    {
        return $this->request('GET', $path, $params);
    }

    /**
     * FORM 表单请求
     */
    public function form($path, array $params = [])
    {
        return $this->request('POST', $path, $params, 'form');
    }

    /**
     * 发送请求
     */
    private function request($method, $path, array $params, $contentType = 'json')
    {
        $params['merchantNo'] = $this->config['merchant_no'];
        $params['sign'] = Utils::sign($params, $this->config['private_key']);

        $url = rtrim($this->config['endpoint'], '/') . $path;
        
        $headers = [
            'Accept: application/json',
            'X-App-Key: ' . $this->config['app_key'],
            'X-Secret-Key: ' . $this->config['private_key']
        ];

        if ($contentType === 'json') {
            $headers[] = 'Content-Type: application/json';
        } else {
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        }

        $ch = curl_init();
        
        if ($method === 'GET') {
            $url .= '?' . http_build_query($params);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } else {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($contentType === 'json') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            }
        }

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => $this->config['timeout'] ?: 30,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['code' => 5000, 'message' => "请求失败: $error"];
        }

        $result = json_decode($response, true);
        return $result ?: ['code' => 5001, 'message' => '响应解析失败'];
    }
}
