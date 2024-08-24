<?php

use kartik\file\FileInput;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Book $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="book-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'year')->textInput(['maxlength' => 4]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'isbn')->textInput(['maxlength' => true]) ?>

    <?=  $form->field($model, 'bookAuthorIds')->widget(Select2::class, [
        'initValueText' => $model->getArrayAuthors(),
        'options' => [
            'multiple' => true,
            'placeholder' => ' Выберите авторов...'
        ],
        'language' => 'ru',
        'maintainOrder' => true,
        'pluginOptions' => [
            'allowClear' => true,
            'templateSelection' => new JsExpression("function (data) { 
                return data.text ? data.text : data.fio;
            }"),
            'ajax' => [
                'url' => Url::to(['author/list']),
                'dataType' => 'json',
                'delay' => 200,
                'data' => new JsExpression("function(params) {
                    return {term: params.term, page: params.page, limit: 20};
                }"),
                'processResults' => new JsExpression("function(data) {
                    return {results: data.results, pagination: { more: data.more }}
                }"),
            ],
        ],
    ]); ?>

    
    <?php if (empty($model->file)) { ?>
        <?= $form->field($model, 'uploadedFile')->widget(FileInput::class, [
            'language' => 'ru',
            'pluginOptions' => [
                'showPreview' => false,
                'showCaption' => true,
                'showRemove' => false,
                'showUpload' => false,
                'allowedFileExtensions' => ['jpg', 'jpeg', 'png'],
            ]   
        ])->hint('Только файлы формата jpg, jpeg, png'); ?>
    <?php } else { ?>
        <div class="form-group">
            <label class="control-label">Фото главной страницы:</label>
            <div class="input-with-button d-flex">
                <?= $form->field($model, 'uploadedFile')->textInput([
                    'value' => $model->getFileName(),
                    'class' => 'form-control',
                    'type' => 'button',
                    'title' => 'Открыть',
                    'onclick' => 'window.open("' . $model->getFilePreview() . '", "_blank");'
                ])->label(false) ?>
                <?= Html::a(
                    'Удалить фото',
                    Url::to(['/book/delete-file', 'id' => $model->id]),
                    [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Вы уверены, что хотите удалить этот файл?',
                            'method' => 'post',
                        ],
                    ]
                ); ?>
            </div>
        </div>
    <?php } ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
