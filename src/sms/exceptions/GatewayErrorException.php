<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\sms\exceptions;

use yii\base\Exception;

/**
 * Class GatewayErrorException
 *
 * @since 3.0
 */
class GatewayErrorException extends Exception
{
    /**
     * @var array
     */
    public $raw = [];

    /**
     * GatewayErrorException constructor.
     * @param string $message
     * @param string $code
     * @param array $raw
     */
    public function __construct($message, $code, array $raw = [])
    {
        parent::__construct($message, intval($code));
        $this->raw = $raw;
    }

    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'SMS Gateway Exception';
    }
}