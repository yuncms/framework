<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\assets;

/**
 *
 */
use yii\web\AssetBundle;

/**
 * Class MetisMenuAsset
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class JqueryMetisMenuAsset extends AssetBundle
{
    /**
     * @inherit
     */
    public $sourcePath = '@vendor/yuncms/framework/resources/lib/metismenu';

    public $css = [
        'metisMenu.min.css',
    ];

    /**
     * @inherit
     */
    public $js = [
        'metisMenu.min.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}