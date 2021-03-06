<?php

namespace yuncms\user\widgets;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yuncms\authclient\ClientInterface;
use yuncms\authclient\widgets\AuthChoice;
use yuncms\authclient\widgets\AuthChoiceAsset;

/**
 * Class Connect
 * @package yuncms\user
 */
class Connect extends AuthChoice
{
    /**
     * @var array|null An array of user's accounts
     */
    public $accounts;
    
    /**
     * @inheritdoc
     */
    public $options = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        AuthChoiceAsset::register(Yii::$app->view);
        if ($this->popupMode) {
            Yii::$app->view->registerJs("\$('#" . $this->getId() . "').authchoice();");
        }
        $this->options['id'] = $this->getId();
        echo Html::beginTag('div', $this->options);
    }

    /**
     * Creates a widget instance and runs it.
     * The widget rendering result is returned by this method.
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @return string the rendering result of the widget.
     */
    public static function widget($config = [])
    {
        try {
            return parent::widget($config);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param ClientInterface $provider
     * @return string
     */
    public function createClientUrl($provider)
    {
        if ($this->isConnected($provider)) {
            return Url::to(['/user/settings/disconnect', 'id' => $this->accounts[$provider->getId()]->id]);
        } else {
            return parent::createClientUrl($provider);
        }
    }

    /**
     * Checks if provider already connected to user.
     *
     * @param ClientInterface $provider
     *
     * @return boolean
     */
    public function isConnected(ClientInterface $provider)
    {
        return $this->accounts != null && isset($this->accounts[$provider->getId()]);
    }
}
