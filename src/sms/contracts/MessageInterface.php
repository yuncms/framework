<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\sms\contracts;

/**
 * Interface MessageInterface
 * @package yuncms\sms\contracts
 */
interface MessageInterface
{
    const TEXT_MESSAGE = 'text';

    const VOICE_MESSAGE = 'voice';

    /**
     * Return the message type.
     *
     * @return string
     */
    public function getMessageType();

    /**
     * Return message content.
     *
     * @param GatewayInterface|null $gateway
     * @return string
     */
    public function getContent(GatewayInterface $gateway = null);

    /**
     * Return the template id of message.
     *
     * @param GatewayInterface|null $gateway
     * @return string
     */
    public function getTemplate(GatewayInterface $gateway = null);

    /**
     * Return the template data of message.
     *
     * @param GatewayInterface|null $gateway
     * @return array
     */
    public function getData(GatewayInterface $gateway = null);

    /**
     * Return message supported gateways.
     *
     * @return array
     */
    public function getGateways();
}