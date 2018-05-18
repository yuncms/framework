<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\sms;

use yii\base\InvalidArgumentException;
use yuncms\sms\contracts\MessageInterface;
use yuncms\sms\exceptions\GatewayErrorException;
use yuncms\sms\exceptions\NoGatewayAvailableException;

/**
 * Class Messenger
 *
 * @since 3.0
 */
class Messenger
{
    const STATUS_SUCCESS = 'success';

    const STATUS_FAILURE = 'failure';

    /**
     * @var Sms
     */
    public $sms;

    /**
     * Messenger constructor.
     *
     * @param Sms $sms
     */
    public function __construct(Sms $sms)
    {
        $this->sms = $sms;
    }

    /**
     * Send a message.
     *
     * @param string|array $to
     * @param string|array|MessageInterface $message
     * @param array $gateways
     * @return array
     * @throws InvalidArgumentException
     * @throws NoGatewayAvailableException
     * @throws \yii\base\InvalidConfigException
     */
    public function send($to, $message, array $gateways = [])
    {
        $message = $this->formatMessage($message);
        if (empty($gateways)) {
            $gateways = $message->getGateways();
        }
        if (empty($gateways)) {
            $gateways = $this->sms->defaultGateway;
        }
        $strategyAppliedGateways = $this->sms->strategy()->apply($gateways);
        $results = [];
        $isSuccessful = false;
        foreach ($strategyAppliedGateways as $gateway) {
            try {
                $results[$gateway] = [
                    'status' => self::STATUS_SUCCESS,
                    'result' => $this->sms->get($gateway)->send($to, $message),
                ];
                $isSuccessful = true;
                break;
            } catch (GatewayErrorException $e) {
                $results[$gateway] = [
                    'status' => self::STATUS_FAILURE,
                    'exception' => $e,
                ];
                continue;
            }
        }
        if (!$isSuccessful) {
            throw new NoGatewayAvailableException($results);
        }
        return $results;
    }

    /**
     * @param array|string|MessageInterface $message
     * @return MessageInterface
     */
    protected function formatMessage($message)
    {
        if (!($message instanceof MessageInterface)) {
            if (!is_array($message)) {
                $message = [
                    'content' => strval($message),
                    'template' => strval($message),
                ];
            }
            $message = new Message($message);
        }
        return $message;
    }
}