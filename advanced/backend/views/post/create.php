<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PostsModel */

$this->title = Yii::t('app', 'Create Posts Model');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Posts Models'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="posts-model-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
