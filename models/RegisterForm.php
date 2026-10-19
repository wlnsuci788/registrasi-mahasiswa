<?php

namespace app\models;

use yii\base\Model;

class RegisterForm extends Model
{
    public const CATEGORY_WNA = 'asing';
    public const CATEGORY_WNI = 'wni';

    public string $jalur = self::CATEGORY_WNA;
    public string $nama = '';
    public string $email = '';
    public string $password = '';
    public string $passwordConfirmation = '';
    public string $consent = '0';

    public function rules(): array
    {
        return [
            [['jalur', 'nama', 'email', 'password', 'passwordConfirmation'], 'required'],
            ['jalur', 'in', 'range' => [self::CATEGORY_WNA, self::CATEGORY_WNI]],
            ['nama', 'string', 'max' => 100],
            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 100],
            ['email', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'email'],
            ['password', 'string', 'min' => 8, 'max' => 255],
            ['passwordConfirmation', 'compare', 'compareAttribute' => 'password', 'message' => 'Konfirmasi kata sandi harus sama.'],
            ['consent', 'compare', 'compareValue' => '1', 'message' => 'Setujui pernyataan sebelum membuat akun.'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'jalur' => 'Kategori calon mahasiswa',
            'nama' => 'Nama lengkap sesuai paspor / KTP',
            'email' => 'Email aktif',
            'password' => 'Kata sandi baru',
            'passwordConfirmation' => 'Konfirmasi kata sandi',
            'consent' => 'Persetujuan',
        ];
    }
}
