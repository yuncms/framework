<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\sms\exceptions;

use Throwable;
use yii\base\Exception;

/**
 * Class NoGatewayAvailableException
 *
 * @since 3.0
 */
class NoGatewayAvailableException extends Exception
{
    /**
     * @var array
     */
    public $results = [];

    /**
     * NoGatewayAvailableException constructor.
     *
     * @param array $results
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(array $results = [], $code = 0, Throwable $previous = null)
    {
        $this->results = $results;
        parent::__construct('All the gateways have failed.', $code, $previous);
    }

    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'SMS Exception';
    }
}