<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yuncms\sms\captcha;

use yii\web\AssetBundle;

/**
 * This asset bundle provides the javascript files needed for the [[Captcha]] widget.
 * @package xutl\sms\captcha
 */
class CaptchaAsset extends AssetBundle
{
    /**
     * @inherit
     */
    public $sourcePath = '@vendor/yuncms/framework/resources/assets/yuncms-sms-captcha';

    public $js = [
        'js/yii.smsCaptcha.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
