<?php

use common\models\Author;
use common\models\Book;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var common\models\BookSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Книги';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->user->identity) { ?>
        <p>
            <?= Html::a('Создать книгу', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php } ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=> 'authorname',
                'format' => 'raw',
                'value' => function (Book $model) {
                    return $model->showAuthor(link: true);
                },
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'authorname',
                    'language' => 'ru',
                    'data' => ArrayHelper::map(Author::find()->all(), 'fio','fio'),
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'Выберите автора'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'formatSelection' => new JsExpression('function (data) {
                            return data.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                        }'),
                    ]
                ])
            ],
            [
                'attribute'=> 'title',
                'format'=> 'raw',
                'value'=> function (Book $model) {
                    return $model->getTitleLink();
                }
            ],
            [
                'attribute'=> 'year',
                'filterInputOptions' => [
                    'type' => 'number',
                    'class' => 'form-control without-arrows'
                ]       
            ],
            'isbn',
            [
                'attribute'=> 'file_id',
                'format' => 'raw',
                'value' => function (Book $model) {
                    return $model->getFileIcon();
                }
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update}{delete}',
                'urlCreator' => function ($action, Book $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'visible' => Yii::$app->user->identity
            ],
        ],
        'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> книг',
        'emptyText' => 'Книг не найдено'
    ]); ?>


</div>
