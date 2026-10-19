<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $user_id
 * @property string $nama
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string|null $role
 * @property int|null $fakultas_id
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Users $createdBy
 * @property DokumenPendaftar[] $dokumenPendaftars
 * @property DokumenPendaftar[] $dokumenPendaftars0
 * @property DokumenPersyaratan[] $dokumenPersyaratans
 * @property DokumenPersyaratan[] $dokumenPersyaratans0
 * @property Fakultas[] $fakultas
 * @property Fakultas[] $fakultas0
 * @property Fakultas $fakultas1
 * @property Pendaftaran[] $pendaftarans
 * @property Pendaftaran[] $pendaftarans0
 * @property Pendaftaran[] $pendaftarans1
 * @property Pendaftaran[] $pendaftarans2
 * @property Pendaftaran[] $pendaftarans3
 * @property PengajuanVisa[] $pengajuanVisas
 * @property PengajuanVisa[] $pengajuanVisas0
 * @property PengajuanVisa[] $pengajuanVisas1
 * @property Users $updatedBy
 * @property Users[] $users
 * @property Users[] $users0
 */
class Users extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const ROLE_CALON_MAHASISWA = 'Calon Mahasiswa';
    const ROLE_INTERNATIONAL_OFFICE = 'International Office';
    const ROLE_FAKULTAS = 'Fakultas';
    const ROLE_UPT_PDK = 'UPT PDK';
    const ROLE_AKADEMIK = 'Akademik';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role', 'fakultas_id', 'created_by', 'updated_by', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['nama', 'email', 'username', 'password'], 'required'],
            [['role'], 'string'],
            [['fakultas_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['nama', 'email'], 'string', 'max' => 100],
            [['username'], 'string', 'max' => 50],
            [['password'], 'string', 'max' => 255],
            ['role', 'in', 'range' => array_keys(self::optsRole())],
            [['email'], 'unique'],
            [['username'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['created_by' => 'user_id']],
            [['fakultas_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fakultas::class, 'targetAttribute' => ['fakultas_id' => 'fakultas_id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['updated_by' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'nama' => 'Nama',
            'email' => 'Email',
            'username' => 'Username',
            'password' => 'Password',
            'role' => 'Role',
            'fakultas_id' => 'Fakultas ID',
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
        return $this->hasMany(DokumenPendaftar::class, ['created_by' => 'user_id']);
    }

    /**
     * Gets query for [[DokumenPendaftars0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDokumenPendaftars0()
    {
        return $this->hasMany(DokumenPendaftar::class, ['updated_by' => 'user_id']);
    }

    /**
     * Gets query for [[DokumenPersyaratans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDokumenPersyaratans()
    {
        return $this->hasMany(DokumenPersyaratan::class, ['created_by' => 'user_id']);
    }

    /**
     * Gets query for [[DokumenPersyaratans0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDokumenPersyaratans0()
    {
        return $this->hasMany(DokumenPersyaratan::class, ['updated_by' => 'user_id']);
    }

    /**
     * Gets query for [[Fakultas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFakultas()
    {
        return $this->hasMany(Fakultas::class, ['created_by' => 'user_id']);
    }

    /**
     * Gets query for [[Fakultas0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFakultas0()
    {
        return $this->hasMany(Fakultas::class, ['updated_by' => 'user_id']);
    }

    /**
     * Gets query for [[Fakultas1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFakultas1()
    {
        return $this->hasOne(Fakultas::class, ['fakultas_id' => 'fakultas_id']);
    }

    /**
     * Gets query for [[Pendaftarans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftarans()
    {
        return $this->hasMany(Pendaftaran::class, ['keputusan_by' => 'user_id']);
    }

    /**
     * Gets query for [[Pendaftarans0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftarans0()
    {
        return $this->hasMany(Pendaftaran::class, ['registration_by' => 'user_id']);
    }

    /**
     * Gets query for [[Pendaftarans1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftarans1()
    {
        return $this->hasMany(Pendaftaran::class, ['saran_by' => 'user_id']);
    }

    /**
     * Gets query for [[Pendaftarans2]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftarans2()
    {
        return $this->hasMany(Pendaftaran::class, ['updated_by' => 'user_id']);
    }

    /**
     * Gets query for [[Pendaftarans3]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftarans3()
    {
        return $this->hasMany(Pendaftaran::class, ['user_id' => 'user_id']);
    }

    /**
     * Gets query for [[PengajuanVisas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPengajuanVisas()
    {
        return $this->hasMany(PengajuanVisa::class, ['created_by' => 'user_id']);
    }

    /**
     * Gets query for [[PengajuanVisas0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPengajuanVisas0()
    {
        return $this->hasMany(PengajuanVisa::class, ['diproses_by' => 'user_id']);
    }

    /**
     * Gets query for [[PengajuanVisas1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPengajuanVisas1()
    {
        return $this->hasMany(PengajuanVisa::class, ['updated_by' => 'user_id']);
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
        return $this->hasMany(Users::class, ['created_by' => 'user_id']);
    }

    /**
     * Gets query for [[Users0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers0()
    {
        return $this->hasMany(Users::class, ['updated_by' => 'user_id']);
    }


    /**
     * column role ENUM value labels
     * @return string[]
     */
    public static function optsRole()
    {
        return [
            self::ROLE_CALON_MAHASISWA => 'Calon Mahasiswa',
            self::ROLE_INTERNATIONAL_OFFICE => 'International Office',
            self::ROLE_FAKULTAS => 'Fakultas',
            self::ROLE_UPT_PDK => 'UPT PDK',
            self::ROLE_AKADEMIK => 'Akademik',
        ];
    }

    /**
     * @return string
     */
    public function displayRole()
    {
        return self::optsRole()[$this->role];
    }

    /**
     * @return bool
     */
    public function isRoleCalonMahasiswa()
    {
        return $this->role === self::ROLE_CALON_MAHASISWA;
    }

    public function setRoleToCalonMahasiswa()
    {
        $this->role = self::ROLE_CALON_MAHASISWA;
    }

    /**
     * @return bool
     */
    public function isRoleInternationalOffice()
    {
        return $this->role === self::ROLE_INTERNATIONAL_OFFICE;
    }

    public function setRoleToInternationalOffice()
    {
        $this->role = self::ROLE_INTERNATIONAL_OFFICE;
    }

    /**
     * @return bool
     */
    public function isRoleFakultas()
    {
        return $this->role === self::ROLE_FAKULTAS;
    }

    public function setRoleToFakultas()
    {
        $this->role = self::ROLE_FAKULTAS;
    }

    /**
     * @return bool
     */
    public function isRoleUptPdk()
    {
        return $this->role === self::ROLE_UPT_PDK;
    }

    public function setRoleToUptPdk()
    {
        $this->role = self::ROLE_UPT_PDK;
    }

    /**
     * @return bool
     */
    public function isRoleAkademik()
    {
        return $this->role === self::ROLE_AKADEMIK;
    }

    public function setRoleToAkademik()
    {
        $this->role = self::ROLE_AKADEMIK;
    }
}
