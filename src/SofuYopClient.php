<?php

namespace Sofu\Yop;

/**
 * 嗖付 YOP PHP SDK 客户端
 *
 * 负责封装基础的参数管理、请求发送以及回调报文解密逻辑。
 */
class SofuYopClient
{
    protected $appKey;
    protected $privateKey;
    protected $endpoint;
    protected $params = [];

    /**
     * 构造函数。
     *
     * @param string $appKey     应用 AppKey，由嗖付平台分配
     * @param string $privateKey 商户私钥或签名密钥
     * @param string $endpoint   嗖付网关地址，默认指向开发者环境
     */
    public function __construct(string $appKey, string $privateKey, string $endpoint = 'https://developer.sofubao.com')
    {
        $this->appKey     = $appKey;
        $this->privateKey = $privateKey;
        $this->endpoint   = rtrim($endpoint, '/');
    }

    /**
     * 增加一个业务参数。
     *
     * 可以多次调用本方法为同一次请求追加参数。
     *
     * @param string $key   参数名
     * @param mixed  $value 参数值
     */
    public function addParam(string $key, $value): void
    {
        $this->params[$key] = $value;
    }

    /**
     * 以 POST 方式请求指定接口路径。
     *
     * @param string $path 接口路径，例如："/api/pay/create"
     *
     * @return array 解码后的 JSON 结果（或错误结构）
     */
    public function post(string $path): array
    {
        return $this->request('POST', $path);
    }

    /**
     * 以 GET 方式请求指定接口路径。
     *
     * @param string $path 接口路径
     *
     * @return array 解码后的 JSON 结果（或错误结构）
     */
    public function get(string $path): array
    {
        return $this->request('GET', $path);
    }

    /**
     * 统一的 HTTP 请求发送方法。
     *
     * 内部负责：
     * - 根据当前已设置的业务参数生成签名；
     * - 组装完整 URL 和请求头；
     * - 使用 curl 发送 GET/POST 请求并返回 JSON 解码后的数组。
     *
     * @param string $method HTTP 方法，只支持 "GET" 或 "POST"
     * @param string $path   接口路径
     *
     * @return array 解码后的 JSON 结果数组，或在异常时返回包含 code/message 的错误结构
     */
    protected function request(string $method, string $path): array
    {
        $url = $this->endpoint . $path;

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-App-Key: ' . $this->appKey,
            'X-Secret-Key: ' . $this->privateKey
        ];

        $this->params['sign'] = $this->generateSign($this->params);

        $curl = curl_init();

        if ($method === 'GET') {
            $url .= '?' . http_build_query($this->params);
        }

        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => $headers,
        ]);

        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->params));
        }

        $response = curl_exec($curl);
        $error    = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return ['code' => 5000, 'message' => "Curl Error: $error"];
        }

        return json_decode($response, true);
    }

    /**
     * 根据当前参数生成签名。
     *
     * 会对参数按键名进行升序排序，然后使用 query string 形式拼接，
     * 最终通过 HMAC-SHA256 算法结合私钥生成签名字符串。
     *
     * @param array $params 参与签名的参数
     *
     * @return string 计算得到的签名字符串
     */
    protected function generateSign(array $params): string
    {
        $params = array_filter($params, function ($value) {
            return !is_null($value) && $value !== '';
        });
        ksort($params);
        $dataString = http_build_query($params);
        return hash_hmac('sha256', $dataString, $this->privateKey);
    }

    /**
     * 解密嗖付回调报文中的加密数据。
     *
     * 解密流程：
     * - 使用回调解密 key 生成 IV；
     * - 对 Base64 编码的密文进行解码；
     * - 使用 AES-256-CBC 算法解密获得明文 JSON；
     * - 将 JSON 解码为数组并返回。
     *
     * @param string $encrypted 回调报文中的 encrypted_data（Base64 字符串）
     * @param string $key       回调解密 key
     *
     * @return array|null 解密并解析后的数组，失败时返回 null
     */
    public function decryptPayload($encrypted, $key)
    {
        $iv = substr(md5($key), 0, 16);
        $decoded = base64_decode($encrypted);
        $decrypted = openssl_decrypt($decoded, 'AES-256-CBC', $key, 0, $iv);
        return json_decode($decrypted, true);
    }

    /**
     * 获取当前已经设置的业务参数。
     *
     * @return array 以键值对形式返回当前参数列表
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
