<?php

use common\models\Book;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Author $model */

$this->title = $model->fio;
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="author-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php if(Yii::$app->user->identity) { ?>
        <p>
            <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены что хотите удалить этого автора?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    <?php } else { ?>
        <p>
            <?=Html::a('Подписаться', ['sub', 'author_id'=> $model->id], ['class' => 'btn btn-primary']) ?>
        </p>
    <?php } ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'fio',
        ],
    ]) ?>

    <div class="author-works">
        <h2>Работы:</h2>
        <?= GridView::widget([
            'dataProvider' => $bookProvider,
            'columns' => [
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
            ],
            'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> работ',
            'emptyText' => 'Работ не найдено'
        ]);
        ?>
    </div>
</div>
