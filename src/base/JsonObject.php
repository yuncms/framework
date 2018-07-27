<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\base;

use yii\base\Arrayable;
use yii\base\InvalidArgumentException;
use yuncms\helpers\Json;

/**
 * Class JsonObject
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class JsonObject implements \ArrayAccess, Arrayable
{
    /**
     * @var array
     */
    protected $value;

    /**
     * @param string|array $value
     */
    public function __construct($value = [])
    {
        $this->set($value);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value ? Json::encode($this->value) : '';
    }

    /**
     * @param string|array $value
     * @throws InvalidArgumentException
     */
    public function set($value)
    {
        if ($value === null || $value === '') {
            $value = [];
        } elseif (is_string($value)) {
            try {
                $value = Json::decode($value, true);
            } catch (InvalidArgumentException $e) {
                throw new InvalidArgumentException($e->getMessage());
            }
        }
        if (!is_array($value)) {
            throw new InvalidArgumentException('Value is not array');
        } else {
            $this->value = $value;
        }
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = array_keys($this->value);
        return array_combine($fields, $fields);
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        return empty($fields) ? $this->value : array_intersect_key($this->value, array_flip($fields));
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return !$this->value;
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return isset($this->value[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function &offsetGet($offset)
    {
        $null = null;
        if (isset($this->value[$offset])) {
            return $this->value[$offset];
        } else {
            return $null;
        }
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->value[] = $value;
        } else {
            $this->value[$offset] = $value;
        }
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        unset($this->value[$offset]);
    }
}