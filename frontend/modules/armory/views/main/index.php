<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>
<?php $form = ActiveForm::begin([
        'id' => 'armory-form',
        'method' => 'get',
        'action' => ['/armory']
    ]); ?>
    <div class="row">
        <div class="col-xs-push-2 col-xs-8 col-sm-push-2 col-sm-8 col-md-push-3 col-md-6 col-push-3 col-lg-6">
            <div class="row">
                <div class="col-md-4">
                    <?php echo $form->field($searchModel, 'server')->dropDownList($searchModel->getServers(),[
                            'prompt' => Yii::t('cp','Выберите сервер'),
                            'name' => 'server',
                        ])->label(false) ?>
                </div>
                <div class="col-md-7">
                    <?php echo $form->field($searchModel, 'query')->label(false) ?>
                </div>
                <div class="col-md-1">
                    <div class="form-group text-center-sm text-center-xs">
                        <?php echo Html::submitButton(Yii::t('common', 'Поиск'), ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>