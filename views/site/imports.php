<?php
use yii\widgets\Pjax;
use yii\grid\GridView;
/* @var $this yii\web\View
 *@var $dataProvider yii\data\ActiveDataProvider
 *
 */

$this->title = 'My Yii Application';
$dataProvider->sort = false;
?>
<div class="site-index">
    <?php

    use yii\widgets\ActiveForm;

    ?>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'store_id',
                'label' => 'Store Title',
                'value' =>  'storeTitle'
            ],
            'total',
            'succeed',
            [
                'label' => 'Wrong',
                'value' =>  'wrong'
            ],
            'status',
            'created_at',
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
