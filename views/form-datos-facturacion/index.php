<?php

use app\models\FormDatosFacturacion;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\bootstrap\Modal;

/** @var yii\web\View $this */
/** @var app\models\FormDatosFacturacionSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Información registro facturas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="form-datos-facturacion-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
       <!--<?= Html::a('Create Form Datos Facturacion', ['create'], ['class' => 'btn btn-success']) ?>-->
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'form_did',
            'form_dnombres_completos',
            'form_ddireccion',
            'form_dfecha',
            'form_dcedula',
            'form_dtelefono',
            'form_dcorreo',
            'form_dfecha_visita',
            //'form_dhora_visita',
            [
                'attribute' => 'form_dhora_visita',
                'value' => function ($model) {
                    return $model->horario->form_hnombre; // Mostrar el nombre del horario
                },
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view}{delete}{change-status}',
                'buttons' => [
                'change-status' => function ($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-refresh"></span>', '#', [
                        'class' => 'change-status-link',
                        'data-url' => Url::to(['change-status', 'id' => $model->form_did]),
                        'data-toggle' => 'modal',
                        'data-target' => '#changeStatusModal',
                    ]);
                    },
                ],
                'urlCreator' => function ($action, FormDatosFacturacion $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'form_did' => $model->form_did]);
                 }
            ],
        ],
    ]); ?>

// Modal
Modal::begin([
    'form_did' => 'changeStatusModal',
    'header' => '<h4>Cambiar Estado</h4>',
]);

echo '<div id="modalContent"></div>';

Modal::end
</div>
