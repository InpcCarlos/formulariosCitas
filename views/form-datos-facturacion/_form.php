<?php 
use yii\helpers\Html;

use yii\widgets\ActiveForm;

use wbraganca\dynamicform\DynamicFormWidget;


/* @var $this yii\web\View */

/* @var $modelCustomer app\modules\yii2extensions\models\Customer */

/* @var $modelsAddress app\modules\yii2extensions\models\Address */


$js = '

jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {

    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
        jQuery(this).html("Address: " + (index + 1))
    });
    //jQuery(item).find("input[name*=\'[form_dvcantidad]\']").val(1);

});


jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {

    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {

        jQuery(this).html("Address: " + (index + 1))

    });

});

';


$this->registerJs($js);

?>


<div class="customer-form">


    <?php $form = ActiveForm::begin(['id' => 'dynamic-form',
    'options' => ['enctype' => 'multipart/form-data','style' => 'width: 100%; max-width: 2000px;']]); ?>
    

    <div class="row">    
    <h3 class="panel-title"><i class="glyphicon glyphicon-envelope"></i> DATOS DE FACTURACIÓN</h3>
    <table class="table table-bordered">
        <tr id="es_operadora_row">
            <td>ES OPERADORA:</td>
            <td colspan="3"><?= $form->field($model, 'form_esoperadora')->dropDownList([1=>'SI','2'=>'NO'],
                                    ["prompt"=>"Seleccione una opción",'required'=>true])->label(false) ?></td>
        </tr>
        <tr id="registro_operadora_row">
            <td>NÚMERO DE REGISTRO DE OPERADORA:</td>
            <td colspan="3"><?= $form->field($model, 'form_registro_operadora')->textInput(['maxlength' => true])->label(false) ?></td>
        </tr>
        <tr>
            <td>NOMBRES COMPLETOS O RAZÓN SOCIAL:</td>
            <td colspan="3"><?= $form->field($model, 'form_dnombres_completos')->textInput(['maxlength' => true])->label(false) ?></td>
        </tr>
        <tr>
            <td>DIRECCIÓN:</td>
            <td colspan="3"><?= $form->field($model, 'form_ddireccion')->textInput(['maxlength' => true])->label(false) ?></td>
        </tr>
        <tr>
            <td>FECHA:</td>
            <td colspan="3"><?= $form->field($model, 'form_dfecha',['inputOptions' => ['type'=>'date','required'=>true, 'value' => date('Y-m-d')]])->textInput()->label(false) ?>            </td>
        </tr>
        <tr>
            <td>No. CÉDULA / RUC:</td>
            <td colspan="3"><?= $form->field($model, 'form_dcedula')->textInput(['maxlength' => 13])->label(false) ?></td>
        </tr>
        <tr>
            <td>TELÉFONO:</td>
            <td colspan="3"><?= $form->field($model, 'form_dtelefono')->textInput(['maxlength' => 10])->label(false) ?></td>
        </tr>
        <tr>
            <td>CORREO ELECTRÓNICO:</td>
            <td colspan="3"><?=  $form->field($model, 'form_dcorreo')->input('email', ['maxlength' => true,'required'=>true])->label(false)?></td>
        </tr>
        <tr>
            <td>FECHA DE VISITA:</td><td><?= $form->field($model, 'form_dfecha_visita',['inputOptions' => ['type'=>'date','required'=>true,"onchange"=>'poblarHorarios(this);','min'=>date('Y-m-d')]])->textInput()->label(false) ?> </td>    
            <td>HORA DE VISITA:</td><td><?= $form->field($model, 'form_dhora_visita')->dropDownList([], ["prompt"=>"Seleccione una opción",'required'=>true,"onchange"=>'poblarTarifario(this);'])->label(false) ?></td>
        </tr>
    </table>

</div>


    <div class="padding-v-md">

        <div class="line line-dashed"></div>

    </div>

    <?php DynamicFormWidget::begin([            
            'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
            'widgetBody' => '.container-items', // required: css class selector
            'widgetItem' => '.item', // required: css class
            'limit' => 10, // the maximum times, an element can be added (default 999)
            'min' => 1, // 0 or 1 (default 1)
            'insertButton' => '.add-item', // css class
            'deleteButton' => '.remove-item', // css class
            'model' => $detalleVisitantes[0],          
            'formId' => 'dynamic-form', //same as your ActiveForm id      
            'formFields' => [
                'form_dvnombres',
                'form_dvapellidos',
                'form_dvcedula',
                'form_dvtipo_visitante',
                'form_dvnacionalidad',
                'form_dvgenero',
                'form_dvfecha_nacimiento',
                'form_dvcantidad',
                'form_dvprecio',
                'form_dvprecio_total',
            ],
        ]); ?>

    <div class="panel panel-default">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <!--<th>Nombre</th>
                    <th>Apellido</th>
                    <th>Cédula</th>-->
                    <th>Origen</th>
                    <th>Tipo Visitante</th>
                    <th>Nacionalidad</th>
                    <th>Género</th>
                    <!--<th>Fecha de Nacimiento</th>-->
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Precio Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="container-items"><!-- widgetContainer -->
                <?php foreach ($detalleVisitantes as $i => $detalleVisitante): ?>
                    <tr class="item panel panel-default"><!-- widgetBody -->
                        <?php
                            if (! $detalleVisitante->isNewRecord) {
                                echo Html::activeHiddenInput($detalleVisitante, "[{$i}]form_dvid");
                            }
                        ?>
                        <td><?= $form->field($detalleVisitante, "[{$i}]form_dvoriginario")->dropDownList([1=>'NACIONAL','2'=>'EXTRANGERO'],
                                    ["prompt"=>"Seleccione una opción",'required'=>true,"onchange"=>'poblarNacionalidad(this);'])->label(false) ?></td>
                        <!--<td><?= $form->field($detalleVisitante, "[{$i}]form_dvnombres")->textInput(['maxlength' => true])->label(false) ?></td>
                        <td><?= $form->field($detalleVisitante, "[{$i}]form_dvapellidos")->textInput(['maxlength' => true])->label(false) ?></td>
                        <td><?= $form->field($detalleVisitante, "[{$i}]form_dvcedula")->textInput(['maxlength' => true])->label(false) ?></td>                        
                        -->
                        <td><?= $form->field($detalleVisitante, "[{$i}]form_dvtipo_visitante")->dropDownList(\yii\helpers\ArrayHelper::map(app\models\FormTipoVisitante::find()->orderBy(['form_tvorden' => SORT_ASC])->all(),
                                    "form_tvid","form_tvnombre"),
                                    ["prompt"=>"Seleccione una opción",'required'=>true,"onchange"=>'poblarTarifario(this);'])->label(false) ?></td>
                        <td><?= $form->field($detalleVisitante, "[{$i}]form_dvnacionalidad")->dropDownList(\yii\helpers\ArrayHelper::map(app\models\FormNacionalidad::find()->all(),
                                    "form_nid","form_nnombre"),
                                    ["prompt"=>"Seleccione una opción",'required'=>true])->label(false) ?></td>                        
                        <td><?= $form->field($detalleVisitante, "[{$i}]form_dvgenero")->dropDownList([1=>'M','2'=>'F'],
                                    ["prompt"=>"Seleccione una opción",'required'=>true])->label(false) ?></td>
                        <!--<td><?= $form->field($detalleVisitante, "[{$i}]form_dvfecha_nacimiento",['inputOptions' => ['type'=>'date','required'=>true,'max'=>date('Y-m-d')]])->textInput()->label(false) ?> </td>
                        -->
                        <td><?= $form->field($detalleVisitante, "[{$i}]form_dvcantidad")->textInput(['maxlength' => true,'class' => 'cantidad-total',"onblur"=>'calcularPrecio(this);'])->label(false) ?></td>
                        <td><?= $form->field($detalleVisitante, "[{$i}]form_dvprecio")->textInput(['maxlength' => true])->label(false) ?></td>
                        <td><?= $form->field($detalleVisitante, "[{$i}]form_dvprecio_total")->textInput(['maxlength' => true,'class' => 'precio-total'])->label(false) ?></td>
                        <td>                            
                            <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus">-</i></button>                            
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="7"></td>
                <td><button type="button" class="add-item btn btn-success btn-sm"><span class="fa fa-plus"></span>Nuevo</button></td>
            </tr>
            <tr>
                    <td colspan="6">TOTAL A PAGAR ($)</td>
                    <td><?= $form->field($model, 'form_dtcantidad')->textInput(['maxlength' => 10])->label(false) ?></td>
                    <td colspan="3"><?= $form->field($model, 'form_dtotal')->textInput(['maxlength' => 10])->label(false) ?></td>
                </tr>
        </tfoot>
        </table>
        <div class="customer-form">
        <div class="row"> 
        <p class="info-message">INFORMACIÓN BANCARIA<br/>
        BENEFICIARIO: INSTITUTO NACIONAL DE PATRIMONIO CULTURAL<br/>
        CUENTA CORRIENTE: 2100010305<br/>
        BANCO: BANCO PICHINCHA<br/>
        RUC: 1760006000001<br/>
        EMAIL: recaudacion@patrimoniocultural.gob.ec<br/>
        </p>

        <?= $form->field($model, 'form_adjunto')->fileInput()->label('COMPROBANTE DE PAGO') ?>
        </div>
        </div>
    </div>

    <?php DynamicFormWidget::end(); ?>

    <div class="form-group">

    <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>