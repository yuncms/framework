<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yuncms\sms\captcha;

use Yii;
use yii\helpers\Url;
use yii\widgets\InputWidget;
use yuncms\helpers\Html;
use yuncms\helpers\Json;

/**
 * Captcha renders a CAPTCHA image and an input field that takes user-entered verification code.
 *
 * Captcha is used together with [[CaptchaAction]] provide [CAPTCHA](http://en.wikipedia.org/wiki/Captcha) - a way
 * of preventing Website spamming.
 *
 * The image element rendered by Captcha will display a CAPTCHA image generated by
 * an action whose route is specified by [[captchaAction]]. This action must be an instance of [[CaptchaAction]].
 *
 * When the user clicks on the CAPTCHA image, it will cause the CAPTCHA image
 * to be refreshed with a new CAPTCHA.
 *
 * You may use [[\yii\captcha\CaptchaValidator]] to validate the user input matches
 * the current CAPTCHA verification code.
 *
 * The following example shows how to use this widget with a model attribute:
 *
 * ```php
 * echo Captcha::widget([
 *     'model' => $model,
 *     'attribute' => 'captcha',
 * ]);
 * ```
 *
 * The following example will use the name property instead:
 *
 * ```php
 * echo Captcha::widget([
 *     'name' => 'captcha',
 * ]);
 * ```
 *
 * You can also use this widget in an [[yii\widgets\ActiveForm|ActiveForm]] using the [[yii\widgets\ActiveField::widget()|widget()]]
 * method, for example like this:
 *
 * ```php
 * <?= $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::classname(), [
 *     // configure additional widget properties here
 * ]) ?>
 * ```
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Captcha extends InputWidget
{
    /**
     * @var string|array the route of the action that generates the CAPTCHA images.
     * The action represented by this route must be an action of [[CaptchaAction]].
     * Please refer to [[\yii\helpers\Url::toRoute()]] for acceptable formats.
     */
    public $captchaAction = 'site/captcha';

    /**
     * @var array HTML attributes to be applied to the CAPTCHA image tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $buttonOptions = [
        'class' => 'btn btn-default '
    ];

    /**
     * @var string the template for arranging the CAPTCHA image tag and the text input tag.
     * In this template, the token `{image}` will be replaced with the actual image tag,
     * while `{input}` will be replaced with the text input tag.
     */
    public $template = '{input} {button}';

    /**
     * @var array the HTML attributes for the input tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = ['class' => 'form-control'];

    /**
     * @var string 手机号码字段
     */
    public $mobileField = '';

    /**
     * @var array 客户端配置参数
     */
    public $clientOptions = [];

    /**
     * Initializes the widget.
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!isset($this->buttonOptions['id'])) {
            $this->buttonOptions['id'] = $this->options['id'] . '-button';
        }
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        if ($this->hasModel()) {
            $input = Html::activeTextInput($this->model, $this->attribute, $this->options);
            $this->mobileField = Html::getInputId($this->model,$this->mobileField);
        } else {
            $input = Html::textInput($this->name, $this->value, $this->options);
            $this->mobileField = $this->name;
        }
        $button = Html::button(Yii::t('yuncms', 'Get Verify Code'), $this->buttonOptions);

        $this->registerClientScript();
        echo strtr($this->template, [
            '{input}' => $input,
            '{button}' => $button,
        ]);
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        $this->clientOptions = array_merge([
            'refreshUrl' => Url::toRoute($this->captchaAction),
            'hashKey'=> 'yiiSmsCaptcha/' . trim($this->captchaAction, '/'),
            'mobileField' => $this->mobileField,
            'buttonTime' => Yii::t('yuncms', 'Resend after @second@ seconds'),
            'buttonGet'=>Yii::t('yuncms', 'Get Verify Code'),
        ], $this->clientOptions);
        $options = empty ($this->clientOptions) ? '' : Json::htmlEncode($this->clientOptions);
        $id = $this->buttonOptions['id'];
        $view = $this->getView();
        CaptchaAsset::register($view);
        $view->registerJs("\r\njQuery('#$id').yiiSmsCaptcha($options);\r\n");
    }
}
