<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\Security;

/**
 * Form login. Bisa login memakai username ATAU email.
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = false;

    private ?User $_user = null;
    private bool $_userLoaded = false;

    public function __construct(private readonly Security $security, $config = [])
    {
        parent::__construct($config);
    }

    /**
     * @return array the validation rules.
     */
    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Username atau Email',
            'password' => 'Password',
            'rememberMe' => 'Ingat saya',
        ];
    }

    /**
     * Memeriksa password. Dipakai sebagai inline validator.
     */
    public function validatePassword($attribute, $params = null): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Email/username atau password salah.');
            }
        }
    }

    /**
     * Login pengguna memakai username/email dan password.
     */
    public function login(): bool
    {
        if ($this->validate()) {
            // Durasi 0 karena enableAutoLogin dimatikan (tabel users belum punya auth_key).
            return Yii::$app->user->login($this->getUser(), 0);
        }

        return false;
    }

    /**
     * Mencari user berdasarkan username atau email.
     */
    public function getUser(): ?User
    {
        if (!$this->_userLoaded) {
            $this->_user = User::findByUsername($this->username);
            $this->_userLoaded = true;
        }

        return $this->_user;
    }
}