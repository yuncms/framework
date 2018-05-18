<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\sms\strategies;

use yuncms\sms\contracts\StrategyInterface;

/**
 * Class RandomStrategy
 *
 * @since 3.0
 */
class RandomStrategy implements StrategyInterface
{
    /**
     * @param array $gateways
     * @return array
     */
    public function apply(array $gateways)
    {
        uasort($gateways, function () {
            return mt_rand() - mt_rand();
        });
        return $gateways;
    }
}