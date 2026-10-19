<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dokumen_persyaratan".
 *
 * @property int $dokumen_id
 * @property string $nama_dokumen
 * @property string|null $berlaku_untuk
 * @property int $wajib
 * @property int $is_active
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Users $createdBy
 * @property DokumenPendaftar[] $dokumenPendaftars
 * @property Users $updatedBy
 */
class DokumenPersyaratan extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const BERLAKU_UNTUK_DEGREE = 'Degree';
    const BERLAKU_UNTUK_NON_DEGREE = 'Non-degree';
    const BERLAKU_UNTUK_SEMUA = 'Semua';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dokumen_persyaratan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['berlaku_untuk', 'created_by', 'updated_by', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['is_active'], 'default', 'value' => 1],
            [['nama_dokumen'], 'required'],
            [['berlaku_untuk'], 'string'],
            [['wajib', 'is_active', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['nama_dokumen'], 'string', 'max' => 100],
            ['berlaku_untuk', 'in', 'range' => array_keys(self::optsBerlakuUntuk())],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['created_by' => 'user_id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['updated_by' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dokumen_id' => 'Dokumen ID',
            'nama_dokumen' => 'Nama Dokumen',
            'berlaku_untuk' => 'Berlaku Untuk',
            'wajib' => 'Wajib',
            'is_active' => 'Is Active',
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
     * Gets query for [[DokumenPendaftars]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDokumenPendaftars()
    {
        return $this->hasMany(DokumenPendaftar::class, ['dokumen_id' => 'dokumen_id']);
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
     * column berlaku_untuk ENUM value labels
     * @return string[]
     */
    public static function optsBerlakuUntuk()
    {
        return [
            self::BERLAKU_UNTUK_DEGREE => 'Degree',
            self::BERLAKU_UNTUK_NON_DEGREE => 'Non-degree',
            self::BERLAKU_UNTUK_SEMUA => 'Semua',
        ];
    }

    /**
     * @return string
     */
    public function displayBerlakuUntuk()
    {
        return self::optsBerlakuUntuk()[$this->berlaku_untuk];
    }

    /**
     * @return bool
     */
    public function isBerlakuUntukDegree()
    {
        return $this->berlaku_untuk === self::BERLAKU_UNTUK_DEGREE;
    }

    public function setBerlakuUntukToDegree()
    {
        $this->berlaku_untuk = self::BERLAKU_UNTUK_DEGREE;
    }

    /**
     * @return bool
     */
    public function isBerlakuUntukNonDegree()
    {
        return $this->berlaku_untuk === self::BERLAKU_UNTUK_NON_DEGREE;
    }

    public function setBerlakuUntukToNonDegree()
    {
        $this->berlaku_untuk = self::BERLAKU_UNTUK_NON_DEGREE;
    }

    /**
     * @return bool
     */
    public function isBerlakuUntukSemua()
    {
        return $this->berlaku_untuk === self::BERLAKU_UNTUK_SEMUA;
    }

    public function setBerlakuUntukToSemua()
    {
        $this->berlaku_untuk = self::BERLAKU_UNTUK_SEMUA;
    }
}
