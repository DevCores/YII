<?php

/**
 * Podium Module
 * Yii 2 Forum Module
 * @author Paweł Bizley Brzozowski <pawel@positive.codes>
 * @since 0.1
 */

use common\modules\forum\helpers\Helper;
use common\modules\forum\Podium;
use common\modules\forum\widgets\Avatar;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('view', 'My Profile');
Yii::$app->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-md-3 col-sm-4">
        <?= $this->render('/elements/profile/_navbar', ['active' => 'profile']) ?>
    </div>
    <div class="col-md-6 col-sm-8">
        <div class="panel panel-default">
            <div class="panel-body">
                <h2>
                    <?= Html::encode($model->podiumName) ?>
                    <small>
                        <?= Html::encode($model->email) ?>
                        <?= Helper::roleLabel($model->role) ?>
                    </small>
                </h2>
                <p><?= Yii::t('view', 'Whereabouts') ?>: <?= !empty($model->meta) && !empty($model->meta->location) ? Html::encode($model->meta->location) : '-' ?></p>
                <p><?= Yii::t('view', 'Member since {date}', ['date' => Podium::getInstance()->formatter->asDatetime($model->created_at, 'long')]) ?> (<?= Podium::getInstance()->formatter->asRelativeTime($model->created_at) ?>)</p>
                <p>
                    <a href="<?= Url::to(['members/threads', 'id' => $model->id, 'slug' => $model->podiumSlug]) ?>" class="btn btn-default"><span class="glyphicon glyphicon-search"></span> <?= Yii::t('view', 'Show all threads started by me') ?></a>
                    <a href="<?= Url::to(['members/posts', 'id' => $model->id, 'slug' => $model->podiumSlug]) ?>" class="btn btn-default"><span class="glyphicon glyphicon-search"></span> <?= Yii::t('view', 'Show all posts created by me') ?></a>
                </p>
            </div>
            <div class="panel-footer">
                <ul class="list-inline">
                    <li><?= Yii::t('view', 'Threads') ?> <span class="badge"><?= $model->threadsCount ?></span></li>
                    <li><?= Yii::t('view', 'Posts') ?> <span class="badge"><?= $model->postsCount ?></span></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-3 hidden-sm hidden-xs">
        <?= Avatar::widget([
            'author' => $model,
            'showName' => false
        ]) ?>
    </div>
</div><br>
