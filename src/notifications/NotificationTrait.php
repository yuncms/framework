<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\notifications;


use yii\helpers\Inflector;
use yuncms\helpers\StringHelper;
use yuncms\notifications\messages\BaseMessage;

/**
 * Trait NotificationTrait
 * @package yuncms\notifications
 */
trait NotificationTrait
{
    public $id;

    /**
     * 确定通知将传送到哪个频道
     * @return array
     */
    public function broadcastOn()
    {
        $channels = [];
        $methods = get_class_methods($this);

        foreach ($methods as $method) {
            if (strpos($method, 'exportFor') === false) {
                continue;
            }
            $channel = str_replace('exportFor', '', $method);
            if (!empty($channel)) {
                $channels[] = Inflector::variablize($channel);
            }
        }
        return $channels;
    }

    /**
     * 将通知作为给定通道的消息导出。
     * ```php
     * public function exportForMail() {
     *      return Yii::createObject([
     *          'class' => 'yuncms\notifications\messages\MailMessage',
     *          'view' => ['html' => 'welcome'],
     *          'viewData' => [...]
     *      ])
     * }
     * ```
     * @param $channel
     * @return BaseMessage
     * @throws \InvalidArgumentException
     */
    public function exportFor($channel)
    {
        if (method_exists($this, $method = 'exportFor' . Inflector::camelize($channel))) {
            return $this->{$method}();
        }
        throw new \InvalidArgumentException("Can not find message export for chanel `{$channel}`");
    }

    /**
     * 导出数据库消息
     */
    public function exportForDatabase()
    {
        $className = get_called_class();
        NotificationModel::create([
            'user_id' => $notification->getUserId(),
            'category' => strtolower(substr($className, strrpos($className, '\\') + 1, -12)),
            'action' => $notification->action,
            'message' => (string)$notification->getTitle(),
            'route' => serialize($notification->getRoute()),
        ]);
    }
}