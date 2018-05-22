<?php
/**
 * @link https://github.com/borodulin/yii2-oauth-server
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-oauth-server/blob/master/LICENSE
 */

namespace yuncms\oauth2\actions;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yuncms\oauth2\GrantType;
use yuncms\oauth2\Exception;
use yuncms\oauth2\grant\types\QRCode;
use yuncms\oauth2\grant\types\RefreshToken;
use yuncms\oauth2\grant\types\Authorization;
use yuncms\oauth2\grant\types\UserCredentials;
use yuncms\oauth2\grant\types\ClientCredentials;
use yuncms\oauth2\grant\types\WeChatCredentials;
use yuncms\oauth2\grant\types\WeChatMiniCredentials;

/**
 * 获取 OAth2 令牌 action
 * ```php
 * public function actions()
 * {
 *     return [
 *         'token' => ['class' => 'yuncms\oauth2\actions\Token'],
 *     ];
 * }
 * ```
 * @author Andrey Borodulin
 */
class Token extends Action
{
    /**
     * Format of response
     * @var string
     */
    public $format = Response::FORMAT_JSON;

    /**
     * @var array Grant Types
     */
    public $grantTypes = [
        'authorization_code' => Authorization::class,
        'refresh_token' => RefreshToken::class,
        'client_credentials' => ClientCredentials::class,//个人账户密码
        'password' => UserCredentials::class,//账户密码
        'wechat' => WeChatCredentials::class,//微信
        'wechat_mini' => WeChatMiniCredentials::class,//微信
        'qrcode' => QRCode::class,//客户端扫码
//        'urn:ietf:params:oauth:grant-type:jwt-bearer' => 'yuncms\oauth2\grant\types\JwtBearer',//JWT 客户端签名
    ];

    /**
     * 初始化
     */
    public function init()
    {
        Yii::$app->response->format = $this->format;
        $this->controller->enableCsrfValidation = false;
    }

    /**
     * run
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        if (!$grantType = GrantType::getRequestValue('grant_type')) {
            throw new Exception(Yii::t('yuncms', 'The grant type was not specified in the request'));
        }
        if (isset($this->grantTypes[$grantType])) {
            $grantModel = Yii::createObject($this->grantTypes[$grantType]);
        } else {
            throw new Exception(Yii::t('yuncms', "An unsupported grant type was requested"), Exception::UNSUPPORTED_GRANT_TYPE);
        }
        $grantModel->validate();
        Yii::$app->response->data = $grantModel->getResponseData();
    }
}