<?php

namespace app\controllers;

use app\models\FormTipoVisitante;
use Yii;
use app\models\FormDatosFacturacion;
use app\models\FormDatosFacturacionSearch;
use app\models\FormDatosVisitante;
use app\models\FormDatosCitas;
use app\models\DynamicModel;
use app\models\FormHorarios;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\swiftmailer\Mailer;

/**
 * FormDatosFacturacionController implements the CRUD actions for FormDatosFacturacion model.
 */
class FormDatosFacturacionController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all FormDatosFacturacion models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new FormDatosFacturacionSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FormDatosFacturacion model.
     * @param string $form_did Form Did
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($form_did)
    {
        $modelFacturacion = $this->findModel($form_did);
        $modelVisitante = FormDatosVisitante::find()->where(['form_did' => $form_did])->all();
        return $this->render('view', [
            'modelFacturacion' => $modelFacturacion,
            'modelVisitante' => $modelVisitante,
        ]);
    }

    /**
     * Creates a new FormDatosFacturacion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */

     public function actionCreate()
    {
    $model = new FormDatosFacturacion();
    $detalleVisitantes = [new FormDatosVisitante()];
    $datosCitas = [new FormDatosCitas()];

    if ($model->load(Yii::$app->request->post())) {
        $model->form_adjunto = UploadedFile::getInstance($model, 'form_adjunto');
        Yii::info("Datos del formulario cargados correctamente: " . print_r($model->attributes, true));
        $detalleVisitantes = DynamicModel::createMultiple(FormDatosVisitante::class);
        DynamicModel::loadMultiple($detalleVisitantes, Yii::$app->request->post());
        Yii::info("Datos de los detalles cargados correctamente");

        // validate all models
        $valid = $model->validate();
        Yii::info("Validación de modelos completada 1: " . $valid);
        $validDetalles = DynamicModel::validateMultiple($detalleVisitantes);
        Yii::info("Validación de modelos completada 2: " . $validDetalles);

        foreach ($detalleVisitantes as $detalleVisitante) {
            if (!$detalleVisitante->validate()) {
                Yii::error("Errores en la validación del detalle: " . print_r($detalleVisitante->errors, true));
            }
        }
        $valid = $validDetalles && $valid;
        if ($valid) {
            $transaction = \Yii::$app->db->beginTransaction();

            try {
                if ($model->save(false)) {
                    Yii::info("form_did asignado: " . $model->form_did);

                    // Asignar form_did a cada detalle
                    foreach ($detalleVisitantes as $detalleVisitante) {
                        $detalleVisitante->form_did = $model->form_did;
                        Yii::info("Guardando detalle con form_did: " . $detalleVisitante->form_did);
                        if (!($detalleVisitante->save(false))) {
                            $transaction->rollBack();
                            Yii::error("Error al guardar el detalle: " . print_r($detalleVisitante->errors, true));
                            break;
                        }
                    }


            /** Almacenar información de adjunto */

            $model->form_adjunto = UploadedFile::getInstance($model, 'form_adjunto');
            if ($model->form_adjunto instanceof UploadedFile) {
                $filePath = 'uploads/' . $model->form_adjunto->baseName . '_' . $model->form_did . '.' . $model->form_adjunto->extension;
                Yii::info("Ruta del archivo adjunto: " . $filePath);
                
                if ($model->form_adjunto->saveAs($filePath)) {
                   
                    $model->form_adjunto = $filePath;                    
                    Yii::info("Valor de form_adjunto antes de guardar el modelo: " . $model->form_adjunto);
                    if (!$model->save(false)) {
                        Yii::error("Errores al guardar el modelo: " . print_r($model->errors, true));
                    } else {
                        Yii::info("El modelo se guardó correctamente con la ruta del archivo: " . $model->form_adjunto);
                    }
                } else {
                    Yii::error("No se pudo guardar el archivo adjunto en la ruta especificada.");
                }
            } else {
                Yii::error("El archivo adjunto no es una instancia de UploadedFile.");
            }

                    
                                        
                    /** Almacenar información de citas */
                    foreach ($datosCitas as $datosCita) {
                        $datosCita->form_did = $model->form_did;
                        $datosCita->form_hid = $model->form_dhora_visita;
                        $datosCita->form_cfecha = $model->form_dfecha_visita;
                        Yii::info("Guardando datos de cita con form_did: " . $datosCita->form_did);
                        if (!($datosCita->save(false))) {
                            $transaction->rollBack();
                            Yii::error("Error al guardar los datos de la cita: " . print_r($datosCita->errors, true));
                            break;
                        }
                    }
                    $transaction->commit();
                    $this-> actionSendNotification($model, $detalleVisitantes);
                    
                    Yii::$app->session->setFlash('success', 'Los datos se almacenaron con éxito.');
                    return $this->refresh();
                } else {
                    Yii::error("Error al guardar el modelo principal: " . print_r($model->errors, true));
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error("Error al guardar los datos: " . $e->getMessage());
            }
        } else {
            Yii::error("Errores del modelo principal: " . print_r($model->errors, true));
            foreach ($detalleVisitantes as $detalleVisitante) {
                Yii::error("Errores del detalle: " . print_r($detalleVisitante->errors, true));
            }
        }
    }
    return $this->render('create', [
        'model' => $model,
        'detalleVisitantes' => (empty($detalleVisitantes)) ? [new FormDatosVisitante] : $detalleVisitantes,
    ]);
}


    /**
     * Updates an existing FormDatosFacturacion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $form_did Form Did
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($form_did)
    {
        $model = $this->findModel($form_did);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'form_did' => $model->form_did]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing FormDatosFacturacion model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $form_did Form Did
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($form_did)
    {
        $this->findModel($form_did)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the FormDatosFacturacion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $form_did Form Did
     * @return FormDatosFacturacion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($form_did)
    {
        if (($model = FormDatosFacturacion::findOne(['form_did' => $form_did])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /*
     * Función que permite retornar el GADM en funciòn al còdigo provincia y código cantòn
     */
    public function actionObtenerhorariosdisponibles()
    {               
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id_val = Yii::$app->request->get('valorid'); // Cambiado a 'get'      
        $val_opera = Yii::$app->request->get('val_opera');            
        $horario = FormHorarios::find()     
                    ->where(['=', 'form_hestado', 1])  
                    ->andWhere(['=', 'form_hoperadora', $val_opera])                                
                    ->orderBy(['form_hid' => SORT_ASC])
                    ->all();
        $html="<option value=''>Seleccione un horario</option>"; 
         
        if(count($horario)>0){           
            foreach($horario AS $clave){
                //print $clave->form_hid;
                
                $cita = FormDatosCitas::find()
                    ->where(['=', 'form_cfecha', $id_val])
                    ->andWhere(['=', 'form_hid', $clave->form_hid])
                    ->count(); 
                    $turno_d =5-$cita;
                    if($turno_d<1)continue;
                    $html .= "<option value='".$clave->form_hid."'>".$clave->form_hnombre." - ".$turno_d." boletos </option>";                    

            }
             }else{
                   $html .="<option value=''></option>";
            }              
        return $html;
    }
    public function actionObtenerpreciotarifa()
    {        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id_val = Yii::$app->request->get('valorid'); // Cambiado a 'get'      
        $tarifa = FormTipoVisitante::find()
                    ->where(['=', 'form_tvid', $id_val])
                    ->one();
        if ($tarifa) {
            return ['precio' => $tarifa->form_tvtarifa];
        } else {
            return ['precio' => 0]; // o cualquier valor por defecto
        }
    }
    /*
     * Función para enviar un email al usuario que factura
     */
    public function actionSendNotification($model, $detalleVisitantes)
    {
        $subject = EMAIL_SUBJECT;
        $nombre_usuario = $model->form_dnombres_completos;
        $fecha_compra = $model->form_dfecha;
        $listaCantidad = "";
        $totalCantidad = 0;
        foreach ($detalleVisitantes as $detalleVisitante) {
            $modelTipoVisitante = FormTipoVisitante::findOne($detalleVisitante->form_dvtipo_visitante);
            $nombreSeleccionado = $modelTipoVisitante ? $modelTipoVisitante->form_tvnombre : null;
            $listaCantidad .= "- $nombreSeleccionado: $detalleVisitante->form_dvcantidad\n";
            $totalCantidad += $detalleVisitante->form_dvcantidad;
        };
        $cantidad_boletos = $totalCantidad;
        $monto_total = $model->form_dtotal;
        $telefono_contacto = Yii::$app->params['contactoINPC'];

        $lista = rtrim($listaCantidad, ", "); 
        $body = <<<EOT
        Estimado/a $nombre_usuario,

        Reciba un cordial saludo.

        Por medio de la presente, le notificamos que su compra de boletos para el **Complejo Arqueológico Ingapirca** ha sido procesada exitosamente. A continuación, encontrará los detalles de su transacción:

        - **Fecha de compra:** $fecha_compra
        - **Cantidad de boletos:** $cantidad_boletos
        $lista
        - **Monto total:** $ $monto_total

        Si requiere asistencia o tiene alguna inquietud, no dude en contactarnos al teléfono $telefono_contacto.

        Agradecemos su visita y esperamos que disfrute de su experiencia en el Complejo Arqueológico Ingapirca.

        Atentamente,

        **Equipo CAI**
        Teléfono: $telefono_contacto
        EOT;

        Yii::$app->mailer->compose()
            ->setTo($model->form_dcorreo)  // Dirección de correo del destinatario
            ->setFrom(Yii::$app->params['caiEmail'])  // Dirección de correo de envío
            ->setSubject($subject)
            ->setTextBody($body)  // Cuerpo del mensaje (puedes usar HTML también con setHtmlBody())
            ->send();

        // Redirigir a otra página o mostrar un mensaje de éxito
        Yii::$app->session->setFlash('success', 'Correo enviado correctamente.');
        return $this->redirect(['index']);  // O la vista que desees
    }
    
       
}
