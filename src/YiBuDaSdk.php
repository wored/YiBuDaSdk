<?php

namespace Wored\YiBuDaSdk;


use Hanson\Foundation\Foundation;

/***
 * @package \Wored\YiBuDaSdk
 *
 * @property \Wored\YiBuDaSdk\Api $api
 */
class YiBuDaSdk extends Foundation
{
    protected $providers = [
        ServiceProvider::class
    ];

    public function __construct($config)
    {
        $config['debug'] = $config['debug'] ?? false;
        parent::__construct($config);
    }

    /**
     * 订单信息创建到一步达
     * @param array $order 推送的数据，以数组形式
     * @param int $declareType 企业报送类型。1-新增 2-变更 3-删除。默认为1。
     * @return bool|mixed
     * @throws \Exception
     */
    public function importOrder(array $order)
    {
        $xml = $this->api->paramToXml($order);
        $content = $this->api->AESencrypt($xml);
        $sign = $this->api->makeSign($xml);
        $param = [
            'content'    => $content,
            'msgType'    => 'IMPORTORDER',
            'dataDigest' => $sign,
            'sendCode'   => $order['mo']['body']['orderInfoList']['orderInfo']['jkfSign']['companyCode'],
        ];
        return $this->api->request($param);
    }
}