<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\FormDatosVisitanteSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="form-datos-visitante-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'form_did') ?>

    <?= $form->field($model, 'form_dvid') ?>

    <?= $form->field($model, 'form_dvnombres') ?>

    <?= $form->field($model, 'form_dvapellidos') ?>

    <?= $form->field($model, 'form_dvcedula') ?>

    <?php // echo $form->field($model, 'form_dvtipo_visitante') ?>

    <?php // echo $form->field($model, 'form_dvnacionalidad') ?>

    <?php // echo $form->field($model, 'form_dvgenero') ?>

    <?php // echo $form->field($model, 'form_dvfecha_nacimiento') ?>

    <?php // echo $form->field($model, 'form_dvcantidad') ?>

    <?php // echo $form->field($model, 'form_dvprecio') ?>

    <?php // echo $form->field($model, 'form_dvprecio_total') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
