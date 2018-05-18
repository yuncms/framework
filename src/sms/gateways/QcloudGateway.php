<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\sms\gateways;

use Yii;
use yii\base\InvalidConfigException;
use yuncms\base\HasHttpRequest;
use yuncms\sms\contracts\MessageInterface;
use yuncms\sms\exceptions\GatewayErrorException;

/**
 * Class AlidayuGateway.
 *
 * @see https://yun.tim.qq.com/v5/tlssmssvr/sendsms?sdkappid=xxxxx&random=xxxx
 *
 * @property string $name
 */
class QcloudGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_URL = 'https://yun.tim.qq.com/v5/';
    const ENDPOINT_METHOD = 'tlssmssvr/sendsms';
    const ENDPOINT_VERSION = 'v5';
    const ENDPOINT_FORMAT = 'json';

    /**
     * @var string 应用ID
     */
    public $appId;

    /**
     * @var string 应用Key
     */
    public $appKey;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (empty ($this->appId)) {
            throw new InvalidConfigException ('The "appId" property must be set.');
        }
        if (empty ($this->appKey)) {
            throw new InvalidConfigException ('The "appKey" property must be set.');
        }
    }

    /**
     * Get gateway name.
     *
     * @return string
     */
    public function getName()
    {
        return 'qcloud';
    }

    /**
     * @param array|int|string $to
     * @param MessageInterface $message
     * @return array
     * @throws GatewayErrorException
     * @throws \yii\base\Exception
     */
    public function send($to, MessageInterface $message)
    {
        $params = [
            'tel' => [
                'nationcode' => $message->getData($this)['nationcode'] ?? '86',
                'mobile' => $to,
            ],
            'type' => $message->getData($this)['type'] ?? 0,
            'msg' => $message->getContent($this),
            'time' => time(),
            'extend' => '',
            'ext' => '',
        ];
        $random = Yii::$app->security->generateRandomString(10);
        $params['sig'] = $this->generateSign($params, $random);
        $url = self::ENDPOINT_URL . self::ENDPOINT_METHOD . '?sdkappid=' . $this->appId . '&random=' . $random;
        $result = $this->request('post', $url, [
            'headers' => ['Accept' => 'application/json'],
            'json' => $params,
        ]);
        if (0 != $result['result']) {
            throw new GatewayErrorException($result['errmsg'], $result['result'], $result);
        }
        return $result;
    }

    /**
     * Generate Sign.
     *
     * @param array $params
     * @param string $random
     * @return string
     */
    protected function generateSign($params, $random)
    {
        ksort($params);
        return hash('sha256', sprintf(
            'appkey=%s&random=%s&time=%s&mobile=%s',
            $this->appKey,
            $random,
            $params['time'],
            $params['tel']['mobile']
        ), false);
    }
}