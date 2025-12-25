<?php

namespace Sofu\Pay\Lib;

/**
 * API 方法 Trait
 */
trait Api
{
    /**
     * 聚合支付统一下单
     */
    public function unifiedOrder($orderId, $orderAmount, $goodsName, $payWay, $channel, $notifyUrl, $options = [])
    {
        return $this->client->post('/zro/pay/unify-pay', array_merge([
            'orderId'     => $orderId,
            'orderAmount' => $orderAmount,
            'goodsName'   => $goodsName,
            'payWay'      => $payWay,
            'channel'     => $channel,
            'notifyUrl'   => $notifyUrl,
        ], $options));
    }

    /**
     * 订单查询
     */
    public function queryOrder($orderNo)
    {
        return $this->client->post('/zro/trade/order-query', ['orderNo' => $orderNo]);
    }

    /**
     * 申请退款
     */
    public function refund($orderNo, $refundMoney, $description = null, $notifyUrl = null)
    {
        $params = ['orderNo' => $orderNo, 'refundMoney' => $refundMoney];
        if ($description) $params['description'] = $description;
        if ($notifyUrl) $params['notifyUrl'] = $notifyUrl;
        return $this->client->post('/zro/trade/refund', $params);
    }

    /**
     * 退款查询
     */
    public function queryRefund()
    {
        return $this->client->post('/zro/trade/refund-query');
    }

    /**
     * 账户余额查询
     */
    public function queryBalance()
    {
        return $this->client->post('/zro/account/balance-query');
    }

    /**
     * 待结算查询
     */
    public function queryPendingSettlement()
    {
        return $this->client->post('/zro/account/settlable-query');
    }

    /**
     * 解密回调数据
     */
    public function decryptCallback($data, $key = null)
    {
        return Utils::decrypt($data, $key ?: $this->decryptKey);
    }
}
