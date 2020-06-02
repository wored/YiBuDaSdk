<?php

namespace Wored\YiBuDaSdk;


use Hanson\Foundation\AbstractAPI;

class Api extends AbstractAPI
{
    public $config = [
        //一步达公钥
        'yibudaPublic' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCffOeIVYut9jW5w1L5uKX4aDvd837a8JhaWm5S8YqNQfgEmfD9T+rDknXLqMT+DXeQAqGo4hBmcbej1aoMzn6hIJHk3/TfTAToNN8fgwDotHewsTCBbVkQWtDTby3GouWToVsRi1i/A0Vfb0+xM8MnF46DdhhrnZrycERBSbyrcwIDAQAB',//一步达公钥
        'RSAmethod'    => OPENSSL_ALGO_SHA1,//签名方法
        //一步达AES密钥
        'yibudaAESkey' => 'qZe60QZFxuirub2ey4+7+Q==',
        'AESmethod'    => 'AES-128-ECB',
    ];

    public function __construct(YiBuDaSdk $yiBuDaSdk)
    {
        $this->config = array_merge($yiBuDaSdk->getConfig(), $this->config);
        $this->config['yibudaPublic'] = $this->keyToResource($this->config['yibudaPublic']);
        $this->config['selfPublic'] = $this->keyToResource($this->config['selfPublic']);
        $this->config['selfPrivate'] = $this->keyToResource($this->config['selfPrivate'], 'PRIVATE');
    }

    /**
     * 秘钥转RSA源
     * @param $key 秘钥
     * @param string $type 秘钥类别
     * @return string
     */
    public function keyToResource($key, $type = 'PUBLIC')
    {
        return "-----BEGIN $type KEY-----\n$key\n-----END $type KEY-----";
    }

    /**
     * 发送请求
     * @param array $params
     * @return bool|mixed
     */
    public function request(array $params)
    {
        $soap = new \SoapClient($this->config['rootUrl']);
        $res = $soap->receive($params);
        return $this->xml_parser($res->return);
    }

    /**
     * AES加密
     * @param string $data 需要加密数据
     * @param string|null $AESkey
     * @return string
     */
    public function AESencrypt(string $data, string $AESkey = null)
    {
        if (empty($AESkey)) {
            //默认平台自己AES密钥
            $AESprivateKey = base64_decode($this->config['selfAESkey']);
        } else {
            $AESprivateKey = base64_decode($AESkey);
        }
        $method = $this->config['AESmethod'];
        $ivlen = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $encData = openssl_encrypt($data, $method, $AESprivateKey, $options = OPENSSL_RAW_DATA, $iv);
        return base64_encode($encData);

    }

    /**
     * AES解密
     * @param $data 需要解密的数据
     * @param null $AESkey
     * @return string
     */
    public function AESdecrypt($data, $AESkey = null)
    {
        if (empty($AESkey)) {
            //默认一步达AES密钥
            $AESprivateKey = base64_decode($this->config['yibudaAESkey']);
        } else {
            $AESprivateKey = base64_decode($AESkey);
        }
        $method = $this->config['AESmethod'];
        $ivlen = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $decryptData = openssl_decrypt(base64_decode($data), $method, $AESprivateKey, OPENSSL_RAW_DATA, $iv);
        return $decryptData;
    }

    /**
     * RSA加密制作签名数据
     * @param string $data
     * @return string
     */
    public function makeSign(string $data)
    {
        $RSAprivateKey = openssl_pkey_get_private($this->config['selfPrivate']);
        openssl_sign($data, $sign, $RSAprivateKey, $this->config['RSAmethod']);
        openssl_free_key($RSAprivateKey);
        return base64_encode($sign);
    }

    /**
     * RSA验证签名
     * @param $content
     * @param $signature
     * @return bool
     */
    public function verifySign(string $content, string $signature)
    {
        $RSApublicKey = openssl_pkey_get_public($this->config['yibudaPublic']);
        $res = (bool)openssl_verify($content, base64_decode($signature), $RSApublicKey, $this->config['RSAmethod']);
        openssl_free_key($RSApublicKey);
        return $res;
    }

    /**
     * 解析XML xml数据解析成数组，字符串直接输出
     * @param $str
     * @return bool|mixed
     */
    function xml_parser(string $str)
    {
        $xml_parser = xml_parser_create();
        if (!xml_parse($xml_parser, $str, true)) {
            xml_parser_free($xml_parser);
            return $str;
        } else {
            return (json_decode(json_encode(simplexml_load_string($str)), true));
        }
    }

    /**
     * 数组转xml
     * @param $param
     * @param bool $root
     * @return string
     */
    public function paramToXml(array $param, $root = true)
    {
        if ($root) {
            $xml = '<?xml version="1.0" encoding="utf-8"?>';
        } else {
            $xml = '';
        }
        foreach ($param as $key => $vo) {
            if ($key === 'attributes') {//判断是否是属性字段
                continue;
            }
            if (!is_numeric($key)) {
                $xml .= "<{$key}";
                if (!empty($vo['attributes'])) {//添加属性
                    foreach ($vo['attributes'] as $item => $attribute) {
                        $xml .= " {$item}=\"{$attribute}\"";
                    }
                }
                $xml .= '>';
            }
            if (is_array($vo) and count($vo) > 0) {
                $xml .= $this->paramToXml($vo, false);
            } else {
                $xml .= $vo;
            }
            if (!is_numeric($key)) {
                $xml .= "</{$key}>";
            }
        }
        return $xml;
    }
}