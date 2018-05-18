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
 * Class YuntongxunGateway
 *
 * @see http://www.yuntongxun.com/doc/rest/sms/3_2_2_2.html
 * @since 3.0
 *
 * @property string $name
 */
class YuntongxunGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_TEMPLATE = 'https://%s:%s/%s/%s/%s/%s/%s?sig=%s';

    const SERVER_IP = 'app.cloopen.com';

    const SERVER_PORT = '8883';

    const SDK_VERSION = '2013-12-26';

    const SUCCESS_CODE = '000000';

    /**
     * @var string 应用ID
     */
    public $appId;

    /**
     * @var string 账号
     */
    public $accountSid;

    /**
     * @var string
     */
    public $accountToken;

    /**
     * @var bool 是否是子账号
     */
    public $isSubAccount = false;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (empty ($this->appId)) {
            throw new InvalidConfigException ('The "appId" property must be set.');
        }
        if (empty ($this->accountSid)) {
            throw new InvalidConfigException ('The "accountSid" property must be set.');
        }
        if (empty ($this->accountToken)) {
            throw new InvalidConfigException ('The "accountToken" property must be set.');
        }
    }

    /**
     * Get gateway name.
     *
     * @return string
     */
    public function getName()
    {
        return 'yuntongxun';
    }

    /**
     * @param array|int|string $to
     * @param MessageInterface $message
     * @return array
     * @throws GatewayErrorException;
     */
    public function send($to, MessageInterface $message)
    {
        $datetime = date('YmdHis');
        $endpoint = $this->buildEndpoint('SMS', 'TemplateSMS', $datetime);
        $result = $this->request('post', $endpoint, [
            'json' => [
                'to' => $to,
                'templateId' => (int)$message->getTemplate($this),
                'appId' => $this->appId,
                'datas' => $message->getData($this),
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=utf-8',
                'Authorization' => base64_encode($this->accountSid . ':' . $datetime),
            ],
        ]);
        if (self::SUCCESS_CODE != $result['statusCode']) {
            throw new GatewayErrorException($result['statusCode'], $result['statusCode'], $result);
        }
        return $result;
    }

    /**
     * Build endpoint url.
     *
     * @param string $type
     * @param string $resource
     * @param string $datetime
     * @return string
     */
    protected function buildEndpoint($type, $resource, $datetime)
    {
        $accountType = $this->isSubAccount ? 'SubAccounts' : 'Accounts';
        $sig = strtoupper(md5($this->accountSid . $this->accountToken . $datetime));
        return sprintf(self::ENDPOINT_TEMPLATE, self::SERVER_IP, self::SERVER_PORT, self::SDK_VERSION, $accountType, $this->accountSid, $type, $resource, $sig);
    }
}