<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\filters;

use Yii;
use yii\base\ActionFilter;
use yuncms\oauth2\GrantType;
use yuncms\oauth2\Exception;

/**
 * Class Authorize
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class OAuth2Authorize extends ActionFilter
{
    /**
     * @var string
     */
    private $_responseType;

    /**
     * @var array
     */
    public $responseTypes = [
        'token' => 'yuncms\oauth2\response\types\Implicit',
        'code' => 'yuncms\oauth2\response\types\Authorization',
    ];

    /**
     *
     * @var boolean
     */
    public $allowImplicit = true;

    /**
     * @var string
     */
    public $storeKey = 'ear6kme7or19rnfldtmwsxgzxsrmngqw';

    /**
     * 初始化
     */
    public function init()
    {
        if (!$this->allowImplicit) {
            unset($this->responseTypes['token']);
        }
    }

    /**
     * Performs OAuth 2.0 request validation and store granttype object in the session,
     * so, user can go from our authorization server to the third party OAuth provider.
     * You should call finishAuthorization() in the current controller to finish client authorization
     * or to stop with Access Denied error message if the user is not logged on.
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function beforeAction($action)
    {
        if (!$responseType = GrantType::getRequestValue('response_type')) {
            throw new Exception(Yii::t('yuncms', 'Invalid or missing response type'));
        }
        if (isset($this->responseTypes[$responseType])) {
            $this->_responseType = Yii::createObject($this->responseTypes[$responseType]);
        } else {
            throw new Exception(Yii::t('yuncms', "An unsupported response type was requested."), Exception::UNSUPPORTED_RESPONSE_TYPE);
        }

        $this->_responseType->validate();

        if ($this->storeKey) {
            Yii::$app->session->set($this->storeKey, serialize($this->_responseType));
        }

        return true;
    }

    /**
     * If user is logged on, do oauth login immediatly,
     * continue authorization in the another case
     * @param \yii\base\Action $action
     * @param mixed $result
     * @return mixed
     * @throws Exception
     */
    public function afterAction($action, $result)
    {
        if (Yii::$app->user->isGuest) {
            return $result;
        } else {
            return $this->finishAuthorization();
        }
    }

    /**
     * @throws Exception
     * @return GrantType
     */
    protected function getResponseType()
    {
        if (empty($this->_responseType) && $this->storeKey) {
            if (Yii::$app->session->has($this->storeKey)) {
                $this->_responseType = unserialize(Yii::$app->session->get($this->storeKey));
            } else {
                throw new Exception(Yii::t('yuncms', 'Invalid server state or the User Session has expired'), Exception::SERVER_ERROR);
            }
        }
        return $this->_responseType;
    }

    /**
     * Finish oauth authorization.
     * Builds redirect uri and performs redirect.
     * If user is not logged on, redirect contains the Access Denied Error
     * @throws Exception
     */
    public function finishAuthorization()
    {
        $responseType = $this->getResponseType();
        if (Yii::$app->user->isGuest) {
            $responseType->errorRedirect(Yii::t('yuncms', 'The User denied access to your application'), Exception::ACCESS_DENIED);
        }
        $parts = $responseType->getResponseData();

        $redirectUri = http_build_url($responseType->redirect_uri, $parts, HTTP_URL_JOIN_QUERY | HTTP_URL_STRIP_FRAGMENT);

        if (isset($parts['fragment'])) {
            $redirectUri .= '#' . $parts['fragment'];
        }

        Yii::$app->response->redirect($redirectUri);
    }

    /**
     * @return boolean
     */
    public function getIsOAuthRequest()
    {
        return !empty($this->storeKey) && Yii::$app->session->has($this->storeKey);
    }
}