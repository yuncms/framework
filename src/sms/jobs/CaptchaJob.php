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
 * Class CaptchaJob
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 *
 * @property int $ttr
 * @property array $templateParam
 */
class CaptchaJob extends BaseObject implements RetryableJobInterface
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
     * @var string 验证码
     */
    public $code;

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
        return '您的验证码为: ' . $this->code;
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
        return [$this->code];
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