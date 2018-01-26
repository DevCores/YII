<?php

namespace frontend\modules\armory\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;

use common\models\chars\Characters;

use frontend\modules\armory\models\CharacterData;

class CharacterController extends Controller
{   
    
    public function beforeAction($action) {
        $parent = parent::beforeAction($action);
        if(!Characters::find()->where(['name' => Yii::$app->request->get('character')])->exists()) {
            Yii::$app->session->setFlash('error',Yii::t('armory','Персонаж не найден!'));
            $this->redirect(Yii::$app->request->referrer ? Yii::$app->request->referrer : Url::to(['/armory']));
            return false;
        }
        
        switch($action->id) {
            case 'index':
                Yii::$app->params['breadcrumbs'][] = ['label' => Yii::$app->request->get('server')];
                Yii::$app->params['breadcrumbs'][] = ['label' => Yii::$app->request->get('character')];
                break;
            case 'talents':
                Yii::$app->params['breadcrumbs'][] = ['label' => Yii::$app->request->get('server')];
                Yii::$app->params['breadcrumbs'][] = ['label' => Yii::$app->request->get('character'),'url' => '/armory/character/' . Yii::$app->request->get('server') . '/' . Yii::$app->request->get('character')];
                Yii::$app->params['breadcrumbs'][] = ['label' => Yii::t('armory','Таланты')];
                break;
        }
        
        return $parent;
    }
    
    /**
     * @return string|\yii\web\Response
     */
    public function actionIndex($server,$character)
    {
        $character = (new CharacterData($character))->data;
        return $this->render('index', [
            'character' => $character,
            'view' => 'character'
        ]);
    }
    
    /**
     * @return string|\yii\web\Response
     */
    public function actionTalents($server,$character)
    {
        $character = (new CharacterData($character))->talents;
        return $this->render('index', [
            'character' => $character,
            'view' => 'talents'
        ]);
    }
}