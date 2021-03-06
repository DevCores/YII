<?php

/**
 * Podium Module
 * Yii 2 Forum Module
 * @author Paweł Bizley Brzozowski <pawel@positive.codes>
 * @since 0.5
 */

use common\modules\forum\widgets\poll\Poll;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('view', 'Edit Poll');
Yii::$app->params['breadcrumbs'][] = ['label' => Yii::t('view', 'Main Forum'), 'url' => ['forum/index']];
Yii::$app->params['breadcrumbs'][] = ['label' => $model->thread->forum->category->name, 'url' => ['forum/category', 'id' => $model->thread->forum->category->id, 'slug' => $model->thread->forum->category->slug]];
Yii::$app->params['breadcrumbs'][] = ['label' => $model->thread->forum->name, 'url' => ['forum/forum', 'cid' => $model->thread->forum->category->id, 'id' => $model->thread->forum->id, 'slug' => $model->thread->forum->slug]];
Yii::$app->params['breadcrumbs'][] = ['label' => $model->thread->name, 'url' => ['forum/thread', 'cid' => $model->thread->forum->category->id, 'fid' => $model->thread->forum->id, 'id' => $model->thread->id, 'slug' => $model->thread->slug]];
Yii::$app->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-sm-10 col-sm-offset-1">
        <div class="panel panel-default">
            <?php $form = ActiveForm::begin(['id' => 'edit-poll-form']); ?>
                <div class="panel-body">
                    <?= Poll::update($form, $model) ?>
                </div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-sm-8">
                            <?= Html::submitButton('<span class="glyphicon glyphicon-ok-sign"></span> ' . Yii::t('view', 'Save Poll'), ['class' => 'btn btn-block btn-primary', 'name' => 'save-button']) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= Html::a('<span class="glyphicon glyphicon-remove"></span> ' . Yii::t('view', 'Cancel'), ['forum/thread', 'cid' => $model->thread->forum->category->id, 'fid' => $model->thread->forum->id, 'id' => $model->thread->id, 'slug' => $model->thread->slug], ['class' => 'btn btn-block btn-default', 'name' => 'cancel-button']) ?>
                        </div>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div><br>
