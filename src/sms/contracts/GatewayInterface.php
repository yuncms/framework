<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\sms\contracts;

use yuncms\sms\exceptions\GatewayErrorException;

/**
 * Interface GatewayInterface
 * @package yuncms\sms\contracts
 */
interface GatewayInterface
{
    /**
     * Get gateway name.
     *
     * @return string
     */
    public function getName();

    /**
     * Send a short message.
     *
     * @param int|string|array $to
     * @param MessageInterface $message
     * @return array
     * @throws GatewayErrorException
     */
    public function send($to, MessageInterface $message);
}