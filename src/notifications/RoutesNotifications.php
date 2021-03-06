<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\notifications;

use Yii;
use yii\helpers\Inflector;
use yuncms\notifications\contracts\NotifiableInterface;

/**
 * Trait RoutesNotificationsTrait
 * @property NotifiableInterface $this
 * @package yuncms\notifications
 */
trait RoutesNotifications
{
    /**
     * Send the given notification.
     *
     * @param  Notification $notification
     * @return void
     * @throws \yii\base\InvalidConfigException
     */
    public function notify($notification)
    {
        Yii::$app->notification->send($this, $notification);
    }

    /**
     * 确定通知实体是否应通过签入通知设置来接收通知。
     * @param Notification $notification
     * @return bool
     */
    public function shouldReceiveNotification(Notification $notification)
    {
        $alias = get_class($notification);
        if (isset($this->notificationSettings)) {
            $settings = $this->notificationSettings;
            if (array_key_exists($alias, $settings)) {
                if ($settings[$alias] instanceof \Closure) {
                    return call_user_func($settings[$alias], $notification);
                }
                return (bool)$settings[$alias];
            }
        }
        return true;
    }

    /**
     * 默认通过电子邮件发送通知
     * @return array
     */
    public function viaChannels()
    {
        return ['database', 'mail'];
    }

    /**
     * 返回给定通道的通知路由信息。
     * ```php
     * public function routeNotificationForMail() {
     *      return $this->email;
     * }
     * ```
     * @param $channel string
     * @return mixed
     */
    public function routeNotificationFor($channel)
    {
        if (method_exists($this, $method = 'routeNotificationFor' . Inflector::camelize($channel))) {
            return $this->{$method}();
        }
        switch ($channel) {
            case 'cloudPush':
                return $this->id;
            case 'mail':
                return $this->email;
            case 'sms':
                return $this->mobile;
        }
        return false;
    }
}