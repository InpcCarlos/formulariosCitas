<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "form_datos_citas".
 *
 * @property int $form_cid
 * @property int|null $form_hid
 * @property int|null $form_did
 * @property int $form_dccantidad
 * @property string|null $form_cfecha
 *
 * @property FormDatosFacturacion $formD
 * @property FormHorarios $formH
 */
class FormDatosCitas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'form_datos_citas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['form_hid', 'form_did'], 'default', 'value' => null],
            [['form_hid', 'form_did','form_dccantidad'], 'integer'],
            [['form_cfecha'], 'safe'],
            [['form_did'], 'exist', 'skipOnError' => true, 'targetClass' => FormDatosFacturacion::class, 'targetAttribute' => ['form_did' => 'form_did']],
            [['form_hid'], 'exist', 'skipOnError' => true, 'targetClass' => FormHorarios::class, 'targetAttribute' => ['form_hid' => 'form_hid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'form_cid' => 'Form Cid',
            'form_hid' => 'Form Hid',
            'form_did' => 'Form Did',
            'form_cfecha' => 'Form Cfecha',
        ];
    }

    /**
     * Gets query for [[FormD]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFormD()
    {
        return $this->hasOne(FormDatosFacturacion::class, ['form_did' => 'form_did']);
    }

    /**
     * Gets query for [[FormH]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFormH()
    {
        return $this->hasOne(FormHorarios::class, ['form_hid' => 'form_hid']);
    }
}
