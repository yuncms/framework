<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\oauth2\response\types;

use Yii;
use yuncms\oauth2\GrantType;
use yuncms\oauth2\models\OAuth2AuthorizationCode;

/**
 * @link https://tools.ietf.org/html/rfc6749#section-4.1.1
 * @author Andrey Borodulin
 */
class Authorization extends GrantType
{
    /**
     * Value MUST be set to "code".
     * @var string
     */
    public $response_type;
    /**
     * Client Identifier
     * @link https://tools.ietf.org/html/rfc6749#section-2.2
     * @var string
     */
    public $client_id;
    /**
     * Redirection Endpoint
     * @link https://tools.ietf.org/html/rfc6749#section-3.1.2
     * @var string
     */
    public $redirect_uri;
    /**
     * Access Token Scope
     * @link https://tools.ietf.org/html/rfc6749#section-3.3
     * @var string
     */
    public $scope;
    /**
     * Cross-Site Request Forgery
     * @link https://tools.ietf.org/html/rfc6749#section-10.12
     * @var string
     */
    public $state;
    public function rules()
    {
        return [
            [['response_type', 'client_id'], 'required'],
            ['response_type', 'required', 'requiredValue' => 'code'],
            [['client_id'], 'string', 'max' => 80],
            [['state'], 'string', 'max' => 255],
            [['redirect_uri'], 'url'],
            [['client_id'], 'validateClientId'],
            [['redirect_uri'], 'validateRedirectUri'],
            [['scope'], 'validateScope'],
        ];
    }

    /**
     * @return array
     * @throws \yii\base\Exception
     * @throws \yuncms\oauth2\Exception
     */
    public function getResponseData()
    {
        $authCode = OAuth2AuthorizationCode::createAuthorizationCode([
            'client_id' => $this->client_id,
            'user_id' => Yii::$app->user->id,
            'expires' => $this->authCodeLifetime,
            'scope' => $this->scope,
            'redirect_uri' => $this->redirect_uri
        ]);
        $query = [
            'code' => $authCode->authorization_code,
        ];
        if (isset($this->state)) {
            $query['state'] = $this->state;
        }
        return [
            'query' => http_build_query($query),
        ];
    }
}
