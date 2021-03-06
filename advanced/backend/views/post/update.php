<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PostsModel */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Posts Model',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Posts Models'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="posts-model-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
