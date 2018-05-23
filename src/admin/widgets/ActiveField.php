<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\admin\widgets;

use Yii;
use yuncms\assets\BootstrapFileStyleAsset;

/**
 * Class ActiveField
 * @package xutl\inspinia
 */
class ActiveField extends \yii\bootstrap\ActiveField
{

    public $options = [
        'class' => 'form-group'
    ];

    /**
     * 显示文件上传窗口
     * @param array $options
     * @return \yii\bootstrap\ActiveField|ActiveField
     */
    public function fileInput($options = [])
    {
        $options = array_merge([
            'class' => 'filestyle',
            'data' => [
                'buttonText' => Yii::t('app', 'Choose file'),
                //'size' => 'lg'
            ]
        ], $options);
        BootstrapFileStyleAsset::register($this->form->view);
        return parent::fileInput($options);
    }

    /**
     * 显示布尔选项
     * @param array $options
     * @return \yii\bootstrap\ActiveField|ActiveField
     */
    public function boolean($options = [])
    {
        return parent::radioList([
            '1' => Yii::t('yii', 'Yes'),
            '0' => Yii::t('yii', 'No')
        ], $options);
    }

    /**
     * 显示布尔选项
     * @param array $options
     * @return \yii\bootstrap\ActiveField|ActiveField
     */
    public function status($options = [])
    {
        return parent::radioList([
            [
                '0' => Yii::t('yuncms', 'Active'),
                '1' => Yii::t('yuncms', 'Disable')
            ]
        ], $options);
    }

    /**
     * @param array $options
     * @param bool $generateDefault
     * @return \yii\bootstrap\ActiveField|ActiveField
     */
    public function dropDownListBool($options = [], $generateDefault = true)
    {
        $items = [
            '0' => Yii::t('yii', 'Yes'),
            '1' => Yii::t('yii', 'No')
        ];
        return $this->dropDownList($items, $options, $generateDefault);
    }

    /**
     * 显示下拉
     * @param array $items
     * @param array $options
     * @param bool $generateDefault
     * @return \yii\bootstrap\ActiveField|ActiveField
     */
    public function dropDownList($items, $options = [], $generateDefault = true)
    {
        if ($generateDefault === true && !isset($options['prompt'])) {
            $options['prompt'] = Yii::t('yuncms', 'Please select');
        }
        return parent::dropDownList($items, $options);
    }

    /**
     * @param array $options
     * @return \yii\bootstrap\ActiveField|ActiveField
     */
    public function textarea($options = [])
    {
        if (!isset($options['rows'])) {
            $options['rows'] = 5;
        }
        return parent::textarea($options);
    }
}