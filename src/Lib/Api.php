<?php

namespace Sofu\Pay\Lib;

/**
 * API 接口
 */
class Api
{
    private $client;
    private $paths;

    public function __construct(HttpClient $client, array $paths)
    {
        $this->client = $client;
        $this->paths = $paths;
    }

    /**
     * 聚合支付统一下单
     */
    public function unifiedOrder($orderId, $orderAmount, $goodsName, $payWay, $channel, $notifyUrl, $options = [])
    {
        $params = array_merge([
            'orderId'     => $orderId,
            'orderAmount' => $orderAmount,
            'goodsName'   => $goodsName,
            'payWay'      => $payWay,
            'channel'     => $channel,
            'notifyUrl'   => $notifyUrl,
        ], $options);

        return $this->client->post($this->paths['unified_order'], $params);
    }

    /**
     * 订单查询
     */
    public function queryOrder($orderNo)
    {
        return $this->client->post($this->paths['query_order'], ['orderNo' => $orderNo]);
    }

    /**
     * 申请退款
     */
    public function refund($orderNo, $refundMoney, $description = null, $notifyUrl = null)
    {
        $params = ['orderNo' => $orderNo, 'refundMoney' => $refundMoney];
        if ($description) $params['description'] = $description;
        if ($notifyUrl) $params['notifyUrl'] = $notifyUrl;
        return $this->client->post($this->paths['refund'], $params);
    }

    /**
     * 退款查询
     */
    public function queryRefund()
    {
        return $this->client->post($this->paths['query_refund']);
    }

    /**
     * 账户余额查询
     */
    public function queryBalance()
    {
        return $this->client->post($this->paths['query_balance']);
    }

    /**
     * 待结算查询
     */
    public function queryPendingSettlement()
    {
        return $this->client->post($this->paths['query_settlement']);
    }
}
