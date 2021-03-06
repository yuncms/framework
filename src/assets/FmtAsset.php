<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\assets;

use yii\web\AssetBundle;

/**
 * Class Asset
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class FmtAsset extends AssetBundle
{
    /**
     * @inherit
     */
    public $sourcePath = '@vendor/yuncms/framework/resources/assets/fmt';

    /**
     * @inherit
     */
    public $css = [
        'fmt.css',
    ];
}