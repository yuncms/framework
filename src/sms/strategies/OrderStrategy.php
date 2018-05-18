<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\sms\strategies;

use yuncms\sms\contracts\StrategyInterface;

/**
 * Class OrderStrategy
 *
 * @since 3.0
 */
class OrderStrategy implements StrategyInterface
{
    /**
     * Apply the strategy and return result.
     *
     * @param array $gateways
     * @return array
     */
    public function apply(array $gateways)
    {
        return $gateways;
    }
}