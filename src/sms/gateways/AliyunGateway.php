<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\sms\gateways;

use yii\base\InvalidConfigException;
use yuncms\base\HasHttpRequest;
use yuncms\sms\contracts\MessageInterface;
use yuncms\sms\exceptions\GatewayErrorException;

/**
 * Class AliyunGateway.
 *
 * @author carson <docxcn@gmail.com>
 *
 * @see https://help.aliyun.com/document_detail/55451.html
 */
class AliyunGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_URL = 'http://dysmsapi.aliyuncs.com';
    const ENDPOINT_METHOD = 'SendSms';
    const ENDPOINT_VERSION = '2017-05-25';
    const ENDPOINT_FORMAT = 'JSON';
    const ENDPOINT_REGION_ID = 'cn-hangzhou';
    const ENDPOINT_SIGNATURE_METHOD = 'HMAC-SHA1';
    const ENDPOINT_SIGNATURE_VERSION = '1.0';

    /**
     * @var string 阿里云AccessKey ID
     */
    public $accessId;

    /**
     * @var string AccessKey
     */
    public $accessKey;

    /**
     * @var string 短信签名
     */
    public $signName;

    /**
     * 初始化短信
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (empty ($this->accessId)) {
            throw new InvalidConfigException ('The "accessId" property must be set.');
        }
        if (empty ($this->accessKey)) {
            throw new InvalidConfigException ('The "accessKey" property must be set.');
        }
        if (empty ($this->signName)) {
            throw new InvalidConfigException ('The "signName" property must be set.');
        }
    }

    /**
     * Get gateway name.
     *
     * @return string
     */
    public function getName()
    {
        return 'aliyun';
    }

    /**
     * @param array|int|string $to
     * @param MessageInterface $message
     * @return array
     * @throws GatewayErrorException;
     */
    public function send($to, MessageInterface $message)
    {
        $params = [
            'RegionId' => self::ENDPOINT_REGION_ID,
            'AccessKeyId' => $this->accessId,
            'Format' => self::ENDPOINT_FORMAT,
            'SignatureMethod' => self::ENDPOINT_SIGNATURE_METHOD,
            'SignatureVersion' => self::ENDPOINT_SIGNATURE_VERSION,
            'SignatureNonce' => uniqid(),
            'Timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'Action' => self::ENDPOINT_METHOD,
            'Version' => self::ENDPOINT_VERSION,
            'PhoneNumbers' => strval($to),
            'SignName' => $this->signName,
            'TemplateCode' => $message->getTemplate($this),
            'TemplateParam' => json_encode($message->getData($this), JSON_FORCE_OBJECT),
        ];
        $params['Signature'] = $this->generateSign($params);
        $result = $this->get(self::ENDPOINT_URL, $params);
        if ('OK' != $result['Code']) {
            throw new GatewayErrorException($result['Message'], $result['Code'], $result);
        }
        return $result;
    }

    /**
     * Generate Sign.
     *
     * @param array $params
     *
     * @return string
     */
    protected function generateSign($params)
    {
        ksort($params);
        $stringToSign = 'GET&%2F&' . urlencode(http_build_query($params, null, '&', PHP_QUERY_RFC3986));
        return base64_encode(hash_hmac('sha1', $stringToSign, $this->accessKey . '&', true));
    }
}