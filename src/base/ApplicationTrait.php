<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\base;

use yii\base\Application;
use yii\web\UrlManager;
use yuncms\notifications\ChannelManager;
use yuncms\services\Path;
use yuncms\sms\Sms;

/**
 * Trait ApplicationTrait
 * @property Application $this
 * @property \yuncms\components\Settings $settings The settings manager component
 * @property \yii\authclient\Collection $authClientCollection The authClient Collection component
 * @property \yii\queue\Queue $queue The queue component
 * @property \yii\redis\Connection $redis The redis component
 * @property \yuncms\filesystem\FilesystemManager $filesystem The filesystem component
 * @property \yuncms\broadcast\BaseBroadcast $broadcast The broadcast component
 * @property \yuncms\notifications\ChannelManager $notification the notification connection.
 * @property \yuncms\mq\BaseMessageQueue $messageQueue the message queue connection.
 * @property \yuncms\sms\Sms $sms the sms connection.
 * @property \yii\web\UrlManager $frontUrlManager the frontUrlManager component.
 * @property \yuncms\services\Path $path the path component.
 * @property \xutl\tim\Tim $im the im component.
 */
trait ApplicationTrait
{
    /**
     * Returns the path component.
     * @return Path the path component.
     */
    public function getPath(): Path
    {
        return $this->get('path');
    }

    /**
     * Returns the sms component.
     * @return UrlManager the frontUrlManager component.
     */
    public function getFrontUrlManager(): UrlManager
    {
        return $this->get('frontUrlManager');
    }

    /**
     * Returns the sms component.
     * @return \yuncms\sms\Sms the sms component.
     */
    public function getSms(): Sms
    {
        return $this->get('sms');
    }

//    /**
//     * @return \yuncms\payment\PaymentManager
//     */
//    public function getPayment(): PaymentManager
//    {
//        return $this->get('payment');
//    }

    /**
     * Returns the message queue component.
     * @return \yuncms\mq\BaseMessageQueue the broadcast connection.
     */
    public function getMessageQueue()
    {
        return $this->get('messageQueue');
    }

    /**
     * Returns the broadcast component.
     * @return \yuncms\broadcast\BaseBroadcast the broadcast connection.
     */
    public function getBroadcast()
    {
        return $this->get('broadcast');
    }

    /**
     * Returns the queue component.
     * @return \yii\queue\Queue the queue connection.
     */
    public function getQueue()
    {
        return $this->get('queue');
    }

    /**
     * Return The redis component
     * @return \yii\redis\Connection
     */
    public function getRedis()
    {
        return $this->get('redis');
    }

    /**
     * Returns the notification component.
     * @return \yuncms\notifications\ChannelManager the notifications connection.
     */
    public function getNotification(): ChannelManager
    {
        return $this->get('notification');
    }

    /**
     * Returns the authClientCollection component.
     * @return \yii\authclient\Collection the authClientCollection connection.
     */
    public function getAuthClientCollection(): \yii\authclient\Collection
    {
        return $this->get('authClientCollection');
    }

    /**
     * Returns the settings component.
     * @return \yuncms\filesystem\FilesystemManager the filesystem connection.
     */
    public function getFilesystem(): \yuncms\filesystem\FilesystemManager
    {
        return $this->get('filesystem');
    }

    /**
     * Returns the settings component.
     * @return \yuncms\components\Settings the settings connection.
     */
    public function getSettings(): \yuncms\components\Settings
    {
        return $this->get('settings');
    }

    /**
     * 给用户发送邮件
     * @param string $to 收件箱
     * @param string $subject 标题
     * @param string $view 视图
     * @param array $params 参数
     * @return boolean
     */
    public function sendMail($to, $subject, $view, $params = []): bool
    {
        if (empty($to)) {
            return false;
        }
        /** @var \yii\mail\MailerInterface $mailer */
        $mailer = $this->getMailer();
        return $mailer->compose(['html' => $view, 'text' => $view], $params)->setTo($to)->setSubject($subject)->send();
    }
}