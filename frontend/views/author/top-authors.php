<?php

use common\models\Author;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */

$this->title = 'Отчет';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-top"></div>

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="filter-form">
        <?php echo Html::beginForm(['top'], 'get'); ?>
        <label for="year">ТОП 10 авторов выпуствиших больше книг за :</label>
        <input type="number" name="year" id="year" value="<?php echo $year; ?>">
        <input type="submit" value="Применить">
        <?php echo Html::endForm(); ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=> 'fio',
                'format' => 'raw',
                'value'=> function (Author $model) {
                    return $model->getTitleLink();
                }
            ],
            [
                'label' => 'Количество',
                'format' => 'raw',
                'value'=> function (Author $model) use ($year) {
                    return $model->getBooksYear($year)->count();
                }
            ],
        ],
    ]); ?>

</div>