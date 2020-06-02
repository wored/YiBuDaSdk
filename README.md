<h1 align="center"> 杭州一步达电子口岸sdk</h1>

## 安装

```shell
$ composer require wored/yibuda-sdk -vvv
```

## 使用
```php
<?php
use \Wored\YiBuDaSdk\YiBuDaSdk;

$config = [
    'rootUrl'       => 'http://122.224.230.4:18003/newyorkWS/ws/ReceiveEncryptDeclare?wsdl',// 测试环境
    'selfPrivate'   => '**********',//平台私钥
    'selfPublic'    => '**********',//平台公钥
    'selfAESkey'    => '**********',//平台AES密钥
    'senderName'    => '杭州保税区',//发件人姓名
    'senderCountry' => '142',//发件人国别
];
$companyCode='**********';//公司海关备案编码
$orderNo='123456789';
// 实例化一步达sdk
$yibuda = new YiBuDaSdk($config);

```
> 订单信息创建到一步达
```php
<?php
   $order = [
       'mo' => [
           'attributes' => [//mo 属性
               'version' => '1.0.0',
           ],
           'head'       => [
               'businessType' => 'IMPORTORDER'
           ],
           'body'       => [
               'orderInfoList' => [
                   'orderInfo' => [
                       'jkfSign'            => [//签名信息
                           'companyCode'  => $companyCode,//必填	发送方备案编号,不可随意填写
                           'businessNo'   => $orderNo,//必填	主要作用是回执给到企业的时候通过这个编号企业能认出对应之前发送的哪个单子
                           'businessType' => 'IMPORTORDER',//必填	业务类型 IMPORTORDER
                           'declareType'  => 1,//必填	企业报送类型。1-新增 2-变更 3-删除。默认为1。
                           'cebFlag'      => '03',//必填	填写或01表示在途在库单证， 02 表示企业采用方案二对接总署版，自行生成加签总署报文， 03表示采用方案一对接，委托平台生成总署报文，回调企业加签服务器加签
                           'note'         => '',//选填 备注
                       ],
                       'jkfOrderImportHead' => [//订单信息
                           'eCommerceCode'    => 'testtest',//必填	电商平台下的商家备案编号
                           'eCommerceName'    => 'testtest',//必填	电商平台下的商家备案名称
                           'ieFlag'           => 'I',//必填	I:进口E:出口
                           'payType'          => '03',//必填	01:银行卡支付 02:余额支付 03:其他
                           'payCompanyCode'   => '31222699S7',//必填	支付平台在跨境平台备案编号
                           'payCompanyName'   => '31222699S7',//必填	支付平台在跨境平台备案编号
                           'payNumber'        => '2020052822001461161403076324',//必填	支付成功后，支付平台反馈给电商平台的支付单号
                           'orderTotalAmount' => 109.1,//必填	货款+订单税款+运费+保费
                           'orderNo'          => $orderNo,//必填 电商平台订单号，注意：一个订单只能对应一个运单(包裹)
                           'orderTaxAmount'   => 9.1,//必填	交易过程中商家向用户征收的税款，按缴税新政计算填写
                           'orderGoodsAmount' => 109.1,//必填	与成交总价一致，按以电子订单的实际销售价格作为完税价格
                           'feeAmount'        => 0,//非必填	交易过程中商家向用户征收的运费，免邮模式填写0
                           'insureAmount'     => 0,//必填	商家向用户征收的保价费用，无保费可填写0
                           'companyName'      => 'testtest',//必填	电商平台在跨境电商通关服务平台的备案名称
                           'companyCode'      => 'test',//必填	电商平台在跨境电商通关服务平台的备案名称
                           'tradeTime'        => date('Y-m-d H:i:s', time() - 3000),//必填	格式：2014-02-18 15:58:11
                           'currCode'         => '142',//必填	见参数表
                           'totalAmount'      => 109.1,//必填	与订单货款一致
                           'consigneeEmail'   => '',//非必填 收件人邮箱
                           'consigneeTel'     => '13123456789',//必填 收件人电话
                           'consignee'        => 'testtest',// 必填 收件人姓名
                           'consigneeAddress' => 'testtest',//必填  收件人地址
                           'totalCount'       => 1,// 必填	包裹中独立包装的物品总数，不考虑物品计量单位
                           'postMode'         => '',//非必填 发货方式（物流方式）
                           'senderCountry'    => '142',//必填 发件人国别
                           'senderName'       => '杭州保税区',// 必填 发件人姓名
                           'purchaserId'      => 'testtest',//必填	购买人在电商平台的注册ID
                           'logisCompanyName' => 'testtest',//必填 物流企业备案名称
                           'logisCompanyCode' => 'testtest',//必填 物流企业备案编号
                           'zipCode'          => '',// 非必填 邮编
                           'note'             => '',//非必填 备注信息
                           'wayBills'         => '',// 非必填 运单之间以分号隔开
                           'rate'             => '1',// 非必填	如果是人民币，统一填写1
                           'discount'         => 0,//必填	使用积分、虚拟货币、代金券等非现金支付金额，无则填写"0"
                           'batchNumbers'     => '',//非必填	商品批次号
                           'consigneeDitrict' => '',//非必填	参照国家统计局公布的国家行政区划标准填制
                           'userProcotol'     => '本人承诺所购买商品系个人合理自用，现委托商家代理申报、代缴税款等通关事宜，本人保证遵守《海关法》和国家相关法律法规，保证所提供的身份信息和收货信息真实完整，无侵犯他人权益的行为，以上委托关系系如实填写，本人愿意接受海关、检验检疫机构及其他监管部门的监管，并承担相应法律责任。',
                           //必填 个人委托申报协议
                       ],
                       'jkfGoodsPurchaser'  => [//购买人信息
                           'id'          => 'testtest',//必填 购买人ID
                           'name'        => 'testtest',//必填 姓名
                           'email'       => '',//非必填 购买人邮箱
                           'telNumber'   => '13123456789',// 必填 联系电话
                           'address'     => '',//非必填 地址
                           'paperType'   => '01',//必填	01:身份证（试点期间只能是身份证）02:护照03:其他
                           'paperNumber' => '110101199003070775',//必填 证件号码
                       ],
                       'jkfOrderDetailList' => [
                           [
                               'jkfOrderDetail' => [
                                   'goodsOrder'    => 1,//必填	商品序号,序号不大于50
                                   'goodsName'     => 'testtest',//必填 物品名称
                                   'goodsModel'    => 'testtest',//非必填 规格型号
                                   'codeTs'        => '0000000000',//必填	填写商品对应的HS编码
                                   'grossWeight'   => '',//非必填 毛重
                                   'unitPrice'     => 109.1,//必填	各商品成交单价*成交数量总和应等于表头的货款、成交总价
                                   'goodsUnit'     => '003',//必填 申报计量单位
                                   'goodsCount'    => 1,// 必填 申报数量
                                   'originCountry' => '142',//必填 产销国
                                   'barCode'       => '',//非必填	国际通用的商品条形码，一般由前缀部分、制造厂商代码、商品代码和校验码组成。
                                   'currency'      => '142',//必填	限定为人民币，填写“142”
                                   'note'          => '',//非必填	促销活动，商品单价偏离市场价格的，可以在此说明。
                               ],
                           ]
                       ],
                   ],
               ],
           ],
       ],
   ];
   $yibuda->createOrder($order);  
```
## License

MIT