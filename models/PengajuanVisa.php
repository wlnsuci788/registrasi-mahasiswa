<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pengajuan_visa".
 *
 * @property int $visa_id
 * @property int|null $pendaftaran_id
 * @property string|null $masa_berlaku_paspor
 * @property string|null $data_pengajuan
 * @property string|null $status
 * @property string|null $catatan
 * @property int|null $diproses_by
 * @property string|null $diproses_at
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Users $createdBy
 * @property Users $diprosesBy
 * @property Pendaftaran $pendaftaran
 * @property Users $updatedBy
 */
class PengajuanVisa extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_MENUNGGU_DIPROSES = 'Menunggu diproses';
    const STATUS_DIPROSES = 'Diproses';
    const STATUS_PERLU_DATA_TAMBAHAN = 'Perlu data tambahan';
    const STATUS_SELESAI = 'Selesai';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pengajuan_visa';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'masa_berlaku_paspor', 'data_pengajuan', 'status', 'catatan', 'diproses_by', 'diproses_at', 'created_by', 'updated_by', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['pendaftaran_id', 'diproses_by', 'created_by', 'updated_by'], 'integer'],
            [['masa_berlaku_paspor', 'diproses_at', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['data_pengajuan', 'status', 'catatan'], 'string'],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['created_by' => 'user_id']],
            [['diproses_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['diproses_by' => 'user_id']],
            [['pendaftaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pendaftaran::class, 'targetAttribute' => ['pendaftaran_id' => 'pendaftaran_id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['updated_by' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'visa_id' => 'Visa ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'masa_berlaku_paspor' => 'Masa Berlaku Paspor',
            'data_pengajuan' => 'Data Pengajuan',
            'status' => 'Status',
            'catatan' => 'Catatan',
            'diproses_by' => 'Diproses By',
            'diproses_at' => 'Diproses At',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Users::class, ['user_id' => 'created_by']);
    }

    /**
     * Gets query for [[DiprosesBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDiprosesBy()
    {
        return $this->hasOne(Users::class, ['user_id' => 'diproses_by']);
    }

    /**
     * Gets query for [[Pendaftaran]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaran()
    {
        return $this->hasOne(Pendaftaran::class, ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(Users::class, ['user_id' => 'updated_by']);
    }


    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_MENUNGGU_DIPROSES => 'Menunggu diproses',
            self::STATUS_DIPROSES => 'Diproses',
            self::STATUS_PERLU_DATA_TAMBAHAN => 'Perlu data tambahan',
            self::STATUS_SELESAI => 'Selesai',
        ];
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusMenungguDiproses()
    {
        return $this->status === self::STATUS_MENUNGGU_DIPROSES;
    }

    public function setStatusToMenungguDiproses()
    {
        $this->status = self::STATUS_MENUNGGU_DIPROSES;
    }

    /**
     * @return bool
     */
    public function isStatusDiproses()
    {
        return $this->status === self::STATUS_DIPROSES;
    }

    public function setStatusToDiproses()
    {
        $this->status = self::STATUS_DIPROSES;
    }

    /**
     * @return bool
     */
    public function isStatusPerluDataTambahan()
    {
        return $this->status === self::STATUS_PERLU_DATA_TAMBAHAN;
    }

    public function setStatusToPerluDataTambahan()
    {
        $this->status = self::STATUS_PERLU_DATA_TAMBAHAN;
    }

    /**
     * @return bool
     */
    public function isStatusSelesai()
    {
        return $this->status === self::STATUS_SELESAI;
    }

    public function setStatusToSelesai()
    {
        $this->status = self::STATUS_SELESAI;
    }
}
