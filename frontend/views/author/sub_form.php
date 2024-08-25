<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Sub $model */

$this->title = 'Подписка на автора';
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->author->fio, 'url' => ['view', 'id' => $model->author->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-sub">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true])->hint('Формат: "79123456789"') ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
