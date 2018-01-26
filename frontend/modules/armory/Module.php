<?php

namespace frontend\modules\armory;

use Yii;
use yii\filters\AccessControl;
use yii\base\BootstrapInterface;
use yii\web\GroupUrlRule;

class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * @var string
     */
    public $controllerNamespace = 'frontend\modules\armory\controllers';
    
    public $defaultRoute = 'main';
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->layout = '@frontend/views/layouts/main_full';
        $this->setAliases(['@armory' => __DIR__]);
        $this->registerTranslations();
    }
    
    public function beforeAction($action) {
        parent::beforeAction($action);
        Yii::$app->params['breadcrumbs'][] = ['label' => Yii::t('frontend','Армори'),'url' => ['/armory/index']];
        return $this;
    }
    
    public function bootstrap($app)
    {
        if ($app instanceof \yii\web\Application) {
            $this->addUrlManagerRules($app);
        }
    }
    
    public function registerTranslations()
    {
        Yii::$app->i18n->translations['armory'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'ru',
            'basePath' => '@armory/messages',
            'on missingTranslation' => ['\backend\modules\i18n\Module', 'missingTranslation']
        ];
    }
    
    protected function addUrlManagerRules($app)
    {
        $app->urlManager->addRules([new GroupUrlRule([
            'prefix' => $this->id,
            'rules' => require __DIR__ . '/url-rules.php',
        ])], true);
    }
    
}