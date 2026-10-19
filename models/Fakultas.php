<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fakultas".
 *
 * @property int $fakultas_id
 * @property string $nama_fakultas
 * @property string|null $kode_fakultas
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Users $createdBy
 * @property Pendaftaran[] $pendaftarans
 * @property Users $updatedBy
 * @property Users[] $users
 */
class Fakultas extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fakultas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kode_fakultas', 'created_by', 'updated_by', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['nama_fakultas'], 'required'],
            [['created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['nama_fakultas'], 'string', 'max' => 100],
            [['kode_fakultas'], 'string', 'max' => 10],
            [['kode_fakultas'], 'unique'],
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
            'fakultas_id' => 'Fakultas ID',
            'nama_fakultas' => 'Nama Fakultas',
            'kode_fakultas' => 'Kode Fakultas',
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
     * Gets query for [[Pendaftarans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftarans()
    {
        return $this->hasMany(Pendaftaran::class, ['fakultas_id' => 'fakultas_id']);
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
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::class, ['fakultas_id' => 'fakultas_id']);
    }

}
