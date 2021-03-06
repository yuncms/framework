<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace yuncms\admin\models;

use Yii;
use yii\db\Query;
use yuncms\db\ActiveRecord;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "{{%admin_menu}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent
 * @property string $route
 * @property string $icon
 * @property integer $visible
 * @property integer $sort
 * @property string $data
 *
 * @property AdminMenu $menuParent
 * @property AdminMenu $parent_name
 * @property AdminMenu[] $menus
 */
class AdminMenu extends ActiveRecord
{
    public $parent_name;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_menu}}';
    }

    public function behaviors()
    {
        return [
            'positionBehavior' => [
                'class' => PositionBehavior::class,
                'positionAttribute' => 'sort',
                'groupAttributes' => [
                    'parent' // multiple lists varying by 'parent'
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parent_name'], 'in',
                'range' => static::find()->select(['name'])->column(),
                'message' => 'Menu "{value}" not found.'],
            [['parent', 'route', 'icon', 'data', 'sort'], 'default'],
            [['parent'], 'filterParent', 'when' => function () {
                return !$this->isNewRecord;
            }],
            [['sort'], 'integer'],
            [['route'], 'in',
                'range' => static::getSavedRoutes(),
                'message' => 'Route "{value}" not found.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('yuncms', 'ID'),
            'name' => Yii::t('yuncms', 'Menu Name'),
            'parent' => Yii::t('yuncms', 'Parent Menu'),
            'route' => Yii::t('yuncms', 'Menu Route'),
            'icon' => Yii::t('yuncms', 'Menu Icon'),
            'visible' => Yii::t('yuncms', 'Visible'),
            'sort' => Yii::t('yuncms', 'Sort'),
            'data' => Yii::t('yuncms', 'Menu Data'),
            'parent_name' => Yii::t('yuncms', 'Parent Menu'),
        ];
    }

    /**
     * Use to loop detected.
     */
    public function filterParent()
    {
        $parent = $this->parent;
        $db = static::getDb();
        $query = (new Query)->select(['parent'])
            ->from(static::tableName())
            ->where('[[id]]=:id');
        while ($parent) {
            if ($this->id == $parent) {
                $this->addError('parent_name', Yii::t('yuncms', 'Loop detected.'));
                return;
            }
            $parent = $query->params([':id' => $parent])->scalar($db);
        }
    }

    /**
     * Get menu parent
     * @return \yii\db\ActiveQuery
     */
    public function getMenuParent()
    {
        return $this->hasOne(self::class, ['id' => 'parent']);
    }

    /**
     * Get menu children
     * @return \yii\db\ActiveQuery
     */
    public function getMenus()
    {
        return $this->hasMany(self::class, ['parent' => 'id']);
    }

    private static $_routes;

    /**
     * Get saved routes.
     * @return array
     */
    public static function getSavedRoutes()
    {
        if (self::$_routes === null) {
            self::$_routes = [];
            foreach (Yii::$app->getAuthManager()->getPermissions() as $name => $value) {
                if ($name[0] === '/' && substr($name, -1) != '*') {
                    self::$_routes[] = $name;
                }
            }
        }
        return self::$_routes;
    }

    public static function getMenuSource()
    {
        $tableName = static::tableName();
        return (new Query())
            ->select(['m.id', 'm.name', 'm.route', 'parent_name' => 'p.name'])
            ->from(['m' => $tableName])
            ->leftJoin(['p' => $tableName], '[[m.parent]]=[[p.id]]')
            ->all(static::getDb());
    }
}
