<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "form_datos_facturacion".
 *
 * @property float $form_did
 * @property string $form_dnombres_completos
 * @property string $form_ddireccion
 * @property string $form_dfecha
 * @property string|null $form_dcedula
 * @property string|null $form_dtelefono
 * @property string|null $form_dcorreo
 * @property string $form_dfecha_visita
 * @property int $form_dhora_visita
 * @property string $form_adjunto
 * @property int $form_esoperadora
 * @property int $form_dtcantidad
 */
class FormDatosFacturacion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'form_datos_facturacion';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['form_dnombres_completos', 'form_ddireccion', 'form_dfecha', 'form_dfecha_visita', 'form_dhora_visita','form_adjunto','form_dtotal'], 'required', 'message' => 'Este campo no puede estar vacío.'],            
            [['form_dfecha', 'form_dfecha_visita'], 'safe'],
            [['form_dhora_visita'], 'default', 'value' => null],
            [['form_dhora_visita','form_esoperadora','form_dtcantidad'], 'integer'],
            [['form_dnombres_completos', 'form_ddireccion'], 'string', 'max' => 200],
            [['form_registro_operadora'], 'string', 'max' => 100],            
            [['form_dcedula'], 'string', 'max' => 150],
            [['form_dtelefono', 'form_dcorreo'], 'string', 'max' => 50],            
            [['form_dcorreo'], 'email','message' => 'La dirección de correo no es válida.'],
            [['form_adjunto'], 'file', 'skipOnEmpty' => true],
            [['form_dtotal'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'form_did' => 'Form Did',
            'form_dnombres_completos' => 'NOMBRES COMPLETOS O RAZÓN SOCIAL',
            'form_ddireccion' => 'DIRECCIÓN',
            'form_dfecha' => 'FECHA',
            'form_dcedula' => 'No. CÉDULA / RUC:',
            'form_dtelefono' => 'TELÉFONO:',
            'form_dcorreo' => 'CORREO: ',
            'form_dfecha_visita' => 'FECHA VISITA',
            'form_dhora_visita' => 'HORA VISITA',
            'form_dtotal' =>'VALOR TOTAL'
        ];
    }
    public function getFormDatosVisitantes() 
    { 
        return $this->hasMany(FormDatosVisitante::class, ['form_did' => 'form_did']);         
    }
    public function getHorario()
    {
        return $this->hasOne(FormHorarios::class, ['form_hid' => 'form_dhora_visita']);
    }
  
}
