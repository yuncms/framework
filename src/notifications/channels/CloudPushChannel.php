<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\notifications\channels;

use Yii;
use yii\base\Component;
use yii\di\Instance;
use yuncms\helpers\Json;
use yuncms\notifications\contracts\ChannelInterface;
use yuncms\notifications\contracts\NotifiableInterface;
use yuncms\notifications\messages\CloudPushMessage;
use xutl\aliyun\Aliyun;
use yuncms\notifications\Notification;

/**
 * App 推送渠道
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class CloudPushChannel extends Component implements ChannelInterface
{
    /**
     * @var string|Aliyun
     */
    public $aliyun = 'aliyun';

    /**
     * @var string
     */
    public $appKey;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->aliyun = Instance::ensure($this->aliyun, Aliyun::class);
    }

    /**
     * Send the given notification.
     *
     * @param  NotifiableInterface $notifiable
     * @param  Notification $notification
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function send(NotifiableInterface $notifiable, Notification $notification)
    {
        /**
         * @var $message CloudPushMessage
         */
        $message = $notification->toCloudPush();
        $appRecipient = $notifiable->routeNotificationFor('cloudPush');
        if ($message->validate()) {
            $messageParams = [
                'AppKey' => $this->appKey,
                'Target' => $appRecipient['target'],
                'TargetValue' => $appRecipient['targetValue'],
                'Title' => $message->title,
                'Body' => $message->content,
            ];
            if (!empty($message->extParameters)) {
                $messageParams['ExtParameters'] = Json::encode($message->extParameters);
            }
            $this->aliyun->getCloudPush()->pushNoticeToAndroid($messageParams);
            $messageParams['ApnsEnv'] = YII_ENV_DEV ? 'DEV' : 'PRODUCT';
            $this->aliyun->getCloudPush()->pushNoticeToIOS($messageParams);
        } else {
            print_r($message->getErrors());
            exit;
        }
    }
}