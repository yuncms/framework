<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\rest;
use yuncms\base\Model;

/**
 * Class ActiveController
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class ActiveController extends \yii\rest\ActiveController
{
    /**
     * 初始化 API 控制器验证
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => 'yii\filters\auth\CompositeAuth',
            'authMethods' => [
                'yii\filters\auth\QueryParamAuth',
                'yuncms\filters\auth\TokenAuth',
            ],
        ];
        return $behaviors;
    }
}