<?php

namespace Sofu\Pay\Lib;

/**
 * API 方法 Trait
 */
trait Api
{
    /**
     * 聚合支付统一下单
     *
     * @param string $orderId     商户订单号
     * @param float  $orderAmount 订单金额（元）
     * @param string $goodsName   商品名称
     * @param string $payWay      支付方式: NATIVE(扫码) | H5_PAY(H5) | MINI_PROGRAM(小程序)
     * @param string $channel     支付渠道: WECHAT | ALIPAY | UNIONPAY
     * @param string $notifyUrl   异步回调地址
     * @param string $userIp      用户IP
     * @param string $returnUrl   支付完成跳转地址（可选）
     * @param string $openId      小程序用户openid（小程序支付必填）
     */
    public function unifiedOrder($orderId, $orderAmount, $goodsName, $payWay, $channel, $notifyUrl, $userIp = null,$expiredTime = null, $returnUrl = null, $openId = null)
    {
        $params = [
            'orderId'     => $orderId,
            'orderAmount' => $orderAmount,
            'goodsName'   => $goodsName,
            'payWay'      => $payWay,
            'channel'     => $channel,
            'notifyUrl'   => $notifyUrl,
            'userIp'      => $userIp,
            'expiredTime' => $expiredTime,
        ];

        if ($returnUrl) $params['returnUrl'] = $returnUrl;
        if ($openId) $params['openId'] = $openId;

        return $this->client->post('/zro/pay/unify-pay', $params);
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
