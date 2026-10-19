<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
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
 * @property-read string $inisial
 * @property-read Fakultas|null $fakultas
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    public const ROLE_CALON    = 'Calon Mahasiswa';
    public const ROLE_IO       = 'International Office';
    public const ROLE_FAKULTAS = 'Fakultas';
    public const ROLE_PDK      = 'UPT PDK';
    public const ROLE_AKADEMIK = 'Akademik';

    public static function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            [['nama', 'email', 'username', 'password'], 'required'],
            [['email'], 'email'],
            [['email', 'username'], 'unique'],
            [['nama', 'email'], 'string', 'max' => 100],
            [['username'], 'string', 'max' => 50],
            [['password'], 'string', 'max' => 255],
            [['role'], 'in', 'range' => [
                self::ROLE_CALON, self::ROLE_IO, self::ROLE_FAKULTAS, self::ROLE_PDK, self::ROLE_AKADEMIK,
            ]],
            [['fakultas_id'], 'integer'],
        ];
    }

    public function getFakultas()
    {
        return $this->hasOne(Fakultas::class, ['fakultas_id' => 'fakultas_id']);
    }

    public function getInisial(): string
    {
        $words = preg_split('/\s+/u', trim((string) $this->nama), -1, PREG_SPLIT_NO_EMPTY);
        $initials = [];

        foreach (array_slice($words ?: [], 0, 2) as $word) {
            if (preg_match('/^\X/u', $word, $matches) === 1) {
                $initials[] = strtoupper($matches[0]);
            }
        }

        return implode('', $initials);
    }

    public static function homeRoute(?string $role): array
    {
        return match ($role) {
            self::ROLE_CALON => ['/calon/index'],
            self::ROLE_IO => ['/io/index'],
            self::ROLE_FAKULTAS => ['/fakultas/index'],
            self::ROLE_PDK => ['/pdk/index'],
            self::ROLE_AKADEMIK => ['/akademik/index'],
            default => ['/site/login'],
        };
    }

    // ---- IdentityInterface ----
    public static function findIdentity($id)
    {
        return static::findOne(['user_id' => $id, 'deleted_at' => null]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public function getId()
    {
        return $this->user_id;
    }

    // Tabel users belum punya kolom auth_key, jadi "ingat saya" tidak dipakai.
    public function getAuthKey()
    {
        return null;
    }

    public function validateAuthKey($authKey)
    {
        return false;
    }

    // ---- Login ----
    // Bisa login memakai username ATAU email (sesuai perancangan).
    public static function findByUsername($username)
    {
        return static::find()
            ->where(['or', ['username' => $username], ['email' => $username]])
            ->andWhere(['deleted_at' => null])
            ->one();
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }
}
