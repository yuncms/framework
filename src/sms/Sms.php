<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\sms;

use Closure;
use Yii;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yuncms\sms\contracts\MessageInterface;
use yuncms\sms\contracts\StrategyInterface;
use yuncms\sms\strategies\OrderStrategy;

/**
 * Class Sms
 *
 * @see https://github.com/overtrue/easy-sms
 * @since 3.0
 *
 * @property string $
 * @property array $gateways
 */
class Sms extends Component
{
    /**
     * @var array gateway parameters (name => value).
     */
    public $params = [];

    /**
     * @var array
     */
    public $defaultGateway;

    /**
     * @var StrategyInterface
     */
    public $defaultStrategy;

    /**
     * @var Messenger
     */
    protected $messenger;

    /**
     * @var array
     */
    protected $strategies = [];

    /**
     * @var array shared gateway instances indexed by their IDs
     */
    private $_gateways = [];

    /**
     * @var array gateway definitions indexed by their IDs
     */
    private $_definitions = [];

    /**
     * Send a message.
     *
     * @param string|array $to
     * @param array|string|MessageInterface $message
     * @param array $gateways
     * @return array
     * @throws InvalidConfigException
     * @throws exceptions\NoGatewayAvailableException
     */
    public function send($to, $message, array $gateways = []): array
    {
        return $this->getMessenger()->send($to, $message, $gateways);
    }

    /**
     * @return Messenger
     */
    public function getMessenger(): Messenger
    {
        return $this->messenger ?: $this->messenger = new Messenger($this);
    }

    /**
     * Get a strategy instance.
     *
     * @param string|null $strategy
     * @return StrategyInterface
     * @throws InvalidArgumentException
     */
    public function strategy($strategy = null)
    {
        if (is_null($strategy)) {
            $strategy = $this->defaultStrategy ?: OrderStrategy::class;
        }

        if (!class_exists($strategy)) {
            $strategy = __NAMESPACE__ . '\strategies\\' . ucfirst($strategy);
        }

        if (!class_exists($strategy)) {
            throw new InvalidArgumentException("Unsupported strategy \"{$strategy}\"");
        }

        if (empty($this->strategies[$strategy]) || !($this->strategies[$strategy] instanceof StrategyInterface)) {
            $this->strategies[$strategy] = new $strategy($this);
        }

        return $this->strategies[$strategy];
    }

    /**
     * Getter magic method.
     * This method is overridden to support accessing gateways like reading properties.
     * @param string $name gateway or property name
     * @return mixed the named property value
     * @throws InvalidConfigException
     * @throws \yii\base\UnknownPropertyException
     */
    public function __get($name)
    {
        if ($this->has($name)) {
            return $this->get($name);
        }

        return parent::__get($name);
    }

    /**
     * Checks if a property value is null.
     * This method overrides the parent implementation by checking if the named gateway is loaded.
     * @param string $name the property name or the event name
     * @return bool whether the property value is null
     */
    public function __isset($name)
    {
        if ($this->has($name)) {
            return true;
        }

        return parent::__isset($name);
    }

    /**
     * Returns a value indicating whether the locator has the specified gateway definition or has instantiated the gateway.
     * This method may return different results depending on the value of `$checkInstance`.
     *
     * - If `$checkInstance` is false (default), the method will return a value indicating whether the locator has the specified
     *   gateway definition.
     * - If `$checkInstance` is true, the method will return a value indicating whether the locator has
     *   instantiated the specified gateway.
     *
     * @param string $id gateway ID (e.g. `local`).
     * @param bool $checkInstance whether the method should check if the gateway is shared and instantiated.
     * @return bool whether the locator has the specified gateway definition or has instantiated the gateway.
     * @see set()
     */
    public function has($id, $checkInstance = false)
    {
        return $checkInstance ? isset($this->_gateways[$id]) : isset($this->_definitions[$id]);
    }

    /**
     * Returns the gateway instance with the specified ID.
     *
     * @param string $id gateway ID (e.g. `db`).
     * @param bool $throwException whether to throw an exception if `$id` is not registered with the locator before.
     * @return object|null the gateway of the specified ID. If `$throwException` is false and `$id`
     * is not registered before, null will be returned.
     * @throws InvalidConfigException if `$id` refers to a nonexistent gateway ID
     * @see has()
     * @see set()
     */
    public function get($id, $throwException = true)
    {
        if (isset($this->_gateways[$id])) {
            return $this->_gateways[$id];
        }

        if (isset($this->_definitions[$id])) {
            $definition = $this->_definitions[$id];
            if (is_object($definition) && !$definition instanceof Closure) {
                return $this->_gateways[$id] = $definition;
            }

            return $this->_gateways[$id] = Yii::createObject($definition);
        } elseif ($throwException) {
            throw new InvalidConfigException("Unknown gateway ID: $id");
        }

        return null;
    }

    /**
     * Registers a gateway definition with this locator.
     *
     * For example,
     *
     * ```php
     * // a class name
     * $locator->set('cache', 'yii\caching\FileCache');
     *
     * // a configuration array
     * $locator->set('db', [
     *     'class' => 'yii\db\Connection',
     *     'dsn' => 'mysql:host=127.0.0.1;dbname=demo',
     *     'username' => 'root',
     *     'password' => '',
     *     'charset' => 'utf8',
     * ]);
     *
     * // an anonymous function
     * $locator->set('cache', function ($params) {
     *     return new \yii\caching\FileCache;
     * });
     *
     * // an instance
     * $locator->set('cache', new \yii\caching\FileCache);
     * ```
     *
     * If a filesystem definition with the same ID already exists, it will be overwritten.
     *
     * @param string $id filesystem ID (e.g. `db`).
     * @param mixed $definition the filesystem definition to be registered with this locator.
     * It can be one of the following:
     *
     * - a class name
     * - a configuration array: the array contains name-value pairs that will be used to
     *   initialize the property values of the newly created object when [[get()]] is called.
     *   The `class` element is required and stands for the the class of the object to be created.
     * - a PHP callable: either an anonymous function or an array representing a class method (e.g. `['Foo', 'bar']`).
     *   The callable will be called by [[get()]] to return an object associated with the specified filesystem ID.
     * - an object: When [[get()]] is called, this object will be returned.
     *
     * @throws InvalidConfigException if the definition is an invalid configuration array
     */
    public function set($id, $definition)
    {
        unset($this->_gateways[$id]);

        if ($definition === null) {
            unset($this->_definitions[$id]);
            return;
        }

        if (is_object($definition) || is_callable($definition, true)) {
            // an object, a class name, or a PHP callable
            $this->_definitions[$id] = $definition;
        } elseif (is_array($definition)) {
            // a configuration array
            if (isset($definition['class'])) {
                $this->_definitions[$id] = $definition;
            } else {
                throw new InvalidConfigException("The configuration for the \"$id\" gateway must contain a \"class\" element.");
            }
        } else {
            throw new InvalidConfigException("Unexpected configuration type for the \"$id\" gateway: " . gettype($definition));
        }
    }

    /**
     * Removes the gateway from the locator.
     * @param string $id the gateway ID
     */
    public function clear($id)
    {
        unset($this->_definitions[$id], $this->_gateways[$id]);
    }

    /**
     * Returns the list of the gateway definitions or the loaded gateway instances.
     * @param bool $returnDefinitions whether to return gateway definitions instead of the loaded gateway instances.
     * @return array the list of the gateway definitions or the loaded gateway instances (ID => definition or instance).
     */
    public function getGateways($returnDefinitions = true)
    {
        return $returnDefinitions ? $this->_definitions : $this->_gateways;
    }

    /**
     * Registers a set of gateway definitions in this locator.
     *
     * This is the bulk version of [[set()]]. The parameter should be an array
     * whose keys are gateway IDs and values the corresponding gateway definitions.
     *
     * For more details on how to specify gateway IDs and definitions, please refer to [[set()]].
     *
     * If a gateway definition with the same ID already exists, it will be overwritten.
     *
     * The following is an example for registering two gateway definitions:
     *
     * ```php
     * [
     *     'local' => [
     *         'class' => 'yuncms\payment\gateways\LocalAdapter',
     *         'path' => '@root/storage',
     *     ],
     * ]
     * ```
     *
     * @param array $gateways gateway definitions or instances
     * @throws InvalidConfigException
     */
    public function setGateways($gateways)
    {
        foreach ($gateways as $id => $gateway) {
            $this->set($id, $gateway);
        }
    }
}