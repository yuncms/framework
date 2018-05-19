<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\admin\models;

use Yii;
use yii\base\Model;

/**
 * Class Settings
 * @package yuncms\admin\models
 */
class Settings extends Model
{
    /**
     * @var string 基础Url
     */
    public $baseUrl;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $keywords;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $copyright;

    /**
     * @var string
     */
    public $close;

    /**
     * @var string
     */
    public $closeReason;

    /**
     * @var string
     */
    public $analysisCode;

    /**
     * @var string
     */
    public $icpBeian;

    /**
     * @var string
     */
    public $beian;

    /**
     * 返回标识
     */
    public function formName()
    {
        return 'system';
    }

    /**
     * 定义字段类型
     * @return array
     */
    public function getTypes()
    {
        return [
            'name' => 'string',
            'title' => 'string',
            'keywords' => 'string',
            'description' => 'string',
            'copyright' => 'string',
            'baseUrl' => 'string',
            'enablePasswordRecovery' => 'string',
            'close' => 'boolean',
            'closeReason' => 'string',
            'analysisCode' => 'string',
            'icpBeian' => 'string',
            'beian' => 'string',
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'title', 'keywords', 'description', 'copyright', 'baseUrl'], 'required'],
            [['name', 'title', 'keywords', 'description', 'copyright', 'closeReason', 'analysisCode', 'icpBeian', 'beian'], 'string'],
            ['close', 'boolean'],
            ['close', 'default', 'value' => false],
            ['baseUrl', 'url'],


            ['name','default','value' => 'YUNCMS'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'baseUrl' => Yii::t('yuncms', 'Base Url'),
            'name' => Yii::t('yuncms', 'Site Name'),
            'title' => Yii::t('yuncms', 'Site Title'),
            'keywords' => Yii::t('yuncms', 'Site Keywords'),
            'description' => Yii::t('yuncms', 'Site Description'),
            'copyright' => Yii::t('yuncms', 'Site Copyright'),
            'close' => Yii::t('yuncms', 'Site Close'),
            'closeReason' => Yii::t('yuncms', 'Site Close Reason'),
            'analysisCode' => Yii::t('yuncms', 'Site Analysis Code'),
            'icpBeian' => Yii::t('yuncms', 'ICP Beian'),
            'beian' => Yii::t('yuncms', 'Beian'),
        ];
    }
}