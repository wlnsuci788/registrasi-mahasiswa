<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dokumen_pendaftar".
 *
 * @property int $dokumen_pendaftar_id
 * @property int|null $pendaftaran_id
 * @property int|null $dokumen_id
 * @property string|null $nama_file
 * @property string|null $file_path
 * @property string|null $uploaded_at
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Users $createdBy
 * @property DokumenPersyaratan|null $dokumen
 * @property Pendaftaran $pendaftaran
 * @property Users $updatedBy
 */
class DokumenPendaftar extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dokumen_pendaftar';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'dokumen_id', 'nama_file', 'file_path', 'uploaded_at', 'created_by', 'updated_by', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['pendaftaran_id', 'dokumen_id', 'created_by', 'updated_by'], 'integer'],
            [['uploaded_at', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['nama_file', 'file_path'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['created_by' => 'user_id']],
            [['dokumen_id'], 'exist', 'skipOnError' => true, 'targetClass' => DokumenPersyaratan::class, 'targetAttribute' => ['dokumen_id' => 'dokumen_id']],
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
            'dokumen_pendaftar_id' => 'Dokumen Pendaftar ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'dokumen_id' => 'Dokumen ID',
            'nama_file' => 'Nama File',
            'file_path' => 'File Path',
            'uploaded_at' => 'Uploaded At',
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
     * Gets query for [[Dokumen]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDokumen()
    {
        return $this->hasOne(DokumenPersyaratan::class, ['dokumen_id' => 'dokumen_id']);
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

}
