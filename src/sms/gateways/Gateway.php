<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\sms\gateways;

use yii\base\BaseObject;
use yuncms\sms\contracts\GatewayInterface;

/**
 * Class Gateway
 *
 * @since 3.0
 */
abstract class Gateway extends BaseObject implements GatewayInterface
{
    const DEFAULT_TIMEOUT = 5.0;

    /**
     * @var float
     */
    protected $timeout;

    /**
     * Return timeout.
     *
     * @return int|mixed
     */
    public function getTimeout()
    {
        return $this->timeout ?: self::DEFAULT_TIMEOUT;
    }

    /**
     * Set timeout.
     *
     * @param int $timeout
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = floatval($timeout);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return '';
    }
}