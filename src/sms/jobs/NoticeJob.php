<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\sms\jobs;


use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\queue\RetryableJobInterface;
use yuncms\sms\exceptions\NoGatewayAvailableException;

/**
 * 通知短信
 * @package yuncms\sms\jobs
 */
class NoticeJob extends BaseObject implements RetryableJobInterface
{
    /**
     * @var string Mobile number
     */
    public $mobile;

    /**
     * @var string
     */
    public $content;

    /**
     * @var string 短信模板
     */
    public $template;

    /**
     * @var array
     */
    public $data;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        try {
            Yii::$app->sms->send($this->mobile, [
                'content' => $this->getContent(),
                'template' => $this->getTemplate(),
                'data' => $this->getTemplateParam()
            ]);
        } catch (InvalidConfigException $e) {

        } catch (NoGatewayAvailableException $e) {

        }
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * 获取模板
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * 获取参数
     * @return array
     */
    public function getTemplateParam()
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function getTtr()
    {
        return 60;
    }

    /**
     * @inheritdoc
     */
    public function canRetry($attempt, $error)
    {
        return $attempt < 3;
    }
}