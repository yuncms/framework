<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\filters\auth;

use Yii;
use yii\web\Response;
use yii\filters\auth\AuthMethod;
use yii\web\UnauthorizedHttpException;
use yuncms\oauth2\models\OAuth2AccessToken;
use yuncms\rest\Controller;

/**
 * TokenAuth is an action filter that supports the authentication method based on the OAuth2 Access Token.
 *
 * You may use TokenAuth by attaching it as a behavior to a controller or module, like the following:
 *
 * ```php
 * public function behaviors()
 * {
 *     return [
 *         'tokenAuth' => [
 *             'class' => \yuncms\filters\auth\OAuth2TokenAuth::className(),
 *         ],
 *     ];
 * }
 * ```
 *
 * @property Controller $owner
 *
 * @author Andrey Borodulin
 */
class OAuth2TokenAuth extends AuthMethod
{
    /**
     * @var OAuth2AccessToken
     */
    private $_accessToken;

    /**
     * @var string the HTTP authentication realm
     */
    public $realm;

    /**
     * @var string the class name of the [[identity]] object.
     */
    public $identityClass;

    /**
     * @param \yii\web\User $user
     * @param \yii\web\Request $request
     * @param Response $response
     * @return null|\yii\web\IdentityInterface|static
     * @throws UnauthorizedHttpException
     */
    public function authenticate($user, $request, $response)
    {
        $accessToken = $this->getAccessToken();
        /* @var \yuncms\user\models\User $identityClass */
        $identityClass = is_null($this->identityClass) ? $user->identityClass : $this->identityClass;
        $identity = $identityClass::findIdentity($accessToken->user_id);
        if (empty($identity)) {
            throw new UnauthorizedHttpException(Yii::t('oauth2', 'User is not found.'));
        }
        $user->setIdentity($identity);
        return $identity;
    }

    /**
     * @param Response $response
     */
    public function challenge($response)
    {
        $realm = empty($this->realm) ? $this->owner->getUniqueId() : $this->realm;
        $response->getHeaders()->set('WWW-Authenticate', "Bearer realm=\"{$realm}\"");
    }

    /**
     * 处理失败返回401
     * @param \yii\web\Response $response
     * @throws UnauthorizedHttpException
     */
    public function handleFailure($response)
    {
        throw new UnauthorizedHttpException(Yii::t('yuncms', 'You are requesting with an invalid credential.'));
    }

    /**
     * 处理认证
     * @return OAuth2AccessToken
     * @throws UnauthorizedHttpException
     */
    protected function getAccessToken()
    {
        if (is_null($this->_accessToken)) {
            $request = Yii::$app->request;
            $authHeader = $request->getHeaders()->get('Authorization');
            $postToken = $request->post('access_token');
            $getToken = $request->get('access_token');

            // Check that exactly one method was used
            $methodsCount = isset($authHeader) + isset($postToken) + isset($getToken);
            if ($methodsCount > 1) {
                throw new UnauthorizedHttpException(Yii::t('yuncms', 'Only one method may be used to authenticate at a time (Auth header, POST or GET).'));
            } elseif ($methodsCount == 0) {
                throw new UnauthorizedHttpException(Yii::t('yuncms', 'The access token was not found.'));
            }
            // HEADER: Get the access token from the header
            if ($authHeader) {
                if (preg_match("/^Bearer\\s+(.*?)$/", $authHeader, $matches)) {
                    $token = $matches[1];
                } else {
                    throw new UnauthorizedHttpException(Yii::t('yuncms', 'Malformed auth header.'));
                }
            } else {
                // POST: Get the token from POST data
                if ($postToken) {
                    if (!$request->isPost) {
                        throw new UnauthorizedHttpException(Yii::t('yuncms', 'When putting the token in the body, the method must be POST.'));
                    }
                    // IETF specifies content-type. NB: Not all webservers populate this _SERVER variable
                    if ($request->contentType != 'application/x-www-form-urlencoded') {
                        throw new UnauthorizedHttpException(Yii::t('yuncms', 'The content type for POST requests must be "application/x-www-form-urlencoded"'));
                    }
                    $token = $postToken;
                } else {
                    $token = $getToken;
                }
            }

            if (!$accessToken = OAuth2AccessToken::findOne(['access_token' => $token])) {
                throw new UnauthorizedHttpException(Yii::t('yuncms', 'The access token provided is invalid.'));
            }
            if ($accessToken->expires < time()) {
                throw new UnauthorizedHttpException(Yii::t('yuncms', 'The access token provided has expired.'));
            }
            $this->_accessToken = $accessToken;
        }
        return $this->_accessToken;
    }
}