<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace yuncms\admin\models;

use Yii;
use yii\rbac\Rule;
use yii\base\Model;

/**
 * Class AdminBizRule
 */
class AdminBizRule extends Model
{
    /**
     * @var string name of the rule
     */
    public $name;
    /**
     * @var integer UNIX timestamp representing the rule creation time
     */
    public $createdAt;
    /**
     * @var integer UNIX timestamp representing the rule updating time
     */
    public $updatedAt;
    /**
     * @var string Rule classname.
     */
    public $className;

    /**
     * @var Rule
     */
    private $_item;


    /**
     * Initilaize object
     * @param \yii\rbac\Rule $item
     * @param array $config
     */
    public function __construct($item, $config = [])
    {
        $this->_item = $item;
        if ($item !== null) {
            $this->name = $item->name;
            $this->className = get_class($item);
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'className'], 'required'],
            [['className'], 'string'],
            [['className'], 'classExists']
        ];
    }

    /**
     * Validate class exists
     */
    public function classExists()
    {
        if (!class_exists($this->className)) {
            $message = Yii::t('yuncms', "Unknown class '{class}'", ['class' => $this->className]);
            $this->addError('className', $message);
            return;
        }
        if (!is_subclass_of($this->className, Rule::class)) {
            $message = Yii::t('yuncms', "'{class}' must extend from 'yii\\rbac\\Rule' or its child class", [
                'class' => $this->className]);
            $this->addError('className', $message);
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('yuncms', 'Rule Name'),
            'className' => Yii::t('yuncms', 'Rule Class Name'),
        ];
    }

    /**
     * Check if new record.
     * @return boolean
     */
    public function getIsNewRecord()
    {
        return $this->_item === null;
    }

    /**
     * Find model by id
     * @param int $id
     * @return null|static
     */
    public static function find($id)
    {
        $item = Yii::$app->getAuthManager()->getRule($id);
        if ($item !== null) {
            return new static($item);
        }

        return null;
    }

    /**
     * Save model to authManager
     * @return boolean
     * @throws \Exception
     */
    public function save()
    {
        if ($this->validate()) {
            $manager = Yii::$app->getAuthManager();
            $class = $this->className;
            if ($this->_item === null) {
                $this->_item = new $class();
                $isNew = true;
            } else {
                $isNew = false;
                $oldName = $this->_item->name;
            }
            $this->_item->name = $this->name;

            if ($isNew) {
                $manager->add($this->_item);
            } else {
                $manager->update($oldName, $this->_item);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Get item
     * @return Rule
     */
    public function getItem()
    {
        return $this->_item;
    }
}