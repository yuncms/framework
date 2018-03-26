<?php

namespace yuncms\actions;

use Yii;
use yii\base\Action;

/**
 * Class SettingsAction
 * @package yuncms\core\actions
 */
class SettingsAction extends Action
{
    /**
     * @var string class name of the model which will be used to validate the attributes.
     * The class should have a scenario matching the `scenario` variable.
     * The model class must implement [[Model]].
     * This property must be set.
     */
    public $modelClass;

    /**
     * @var string The scenario this model should use to make validation
     */
    public $scenario;

    /**
     * @var string the name of the view to generate the form. Defaults to 'settings'.
     */
    public $viewName = 'settings';

    /**
     * Render the settings form.
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        /* @var $model \yii\db\ActiveRecord */
        $model = new $this->modelClass();
        if ($this->scenario) {
            $model->setScenario($this->scenario);
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            foreach ($model->toArray() as $key => $value) {
                //Yii::$app->settings->set($key, $value, $this->controller->module->id, $model->types[$key]);
                Yii::$app->settings->set($key, $value, $model->formName(), $model->types[$key]);
            }
            Yii::$app->getSession()->addFlash('success', Yii::t('core', 'Successfully saved settings.'));
            return $this->controller->redirect([$this->controller->action->id]);
        }
        foreach ($model->attributes() as $key) {
            $model->{$key} = Yii::$app->settings->get($key, $model->formName());
            //$model->{$key} = Yii::$app->settings->get($key, $this->controller->module->id);//直接使用模块ID
        }
        return $this->controller->render($this->viewName, ['model' => $model]);
    }
}
