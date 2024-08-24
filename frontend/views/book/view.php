<?php

use common\models\Book;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Book $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="book-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->user->identity) { ?>
        <p>
            <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены что хотите удалить эту книгу?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    <?php } ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'title',
            'year',
            'description',
            'isbn',
            [
                'label' => 'Автор(ы)',
                'format' => 'raw',
                'value' => function(Book $model) {
                    return $model->showAuthor(link: true);
                },
            ],
            [
                'attribute' => 'file_id',
                'format' => 'raw',
                'value' => function(Book $model) {
                    return $model->getFileIcon();
                }
            ],
        ],
    ]) ?>

</div>
