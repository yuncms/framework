<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\rest\controllers;

use Yii;
use yii\web\ServerErrorHttpException;
use yuncms\rest\Controller;
use yuncms\rest\models\UploaderAudioForm;
use yuncms\rest\models\UploaderFileForm;
use yuncms\rest\models\UploaderImageForm;

/**
 * Class UploaderController
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class UploaderController extends Controller
{
    /**
     * 图片上传
     * @return UploaderImageForm
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function actionImage()
    {
        $model = new UploaderImageForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save() != false) {
            Yii::$app->getResponse()->setStatusCode(201);
            return $model;
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }

    /**
     * 语音上传
     * @return UploaderAudioForm
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function actionAudio()
    {
        $model = new UploaderAudioForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save() != false) {
            Yii::$app->getResponse()->setStatusCode(201);
            return $model;
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }

    /**
     * 文件上传
     * @return UploaderFileForm
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function actionFile()
    {
        $model = new UploaderFileForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save() != false) {
            Yii::$app->getResponse()->setStatusCode(201);
            return $model;
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }
}