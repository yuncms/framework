<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\payment\gateways;

use Yii;

/**
 * Class Alipay
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class Alipay extends BaseAliPay
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('yuncms', 'Alipay');
    }

}