<?php

namespace app\commands;

use app\models\Fakultas;
use app\models\User;
use yii\console\Controller;
use yii\console\ExitCode;

class FakultasDemoController extends Controller
{
    public function actionIndex(): int
    {
        if (!YII_ENV_DEV) {
            throw new \RuntimeException('Akun demo Fakultas hanya boleh disiapkan di lingkungan development.');
        }

        $faculty = Fakultas::find()
            ->where(['nama_fakultas' => 'Fakultas Kedokteran', 'deleted_at' => null])
            ->one();
        if (!$faculty) {
            throw new \RuntimeException(
                'Fakultas Kedokteran belum terdaftar. Siapkan data fakultas terlebih dahulu.',
            );
        }

        $username = 'fk-demo';
        $email = 'fk.demo@unand.ac.id';
        $user = User::findOne(['username' => $username, 'deleted_at' => null]);
        if (!$user) {
            $emailOwner = User::findOne(['email' => $email, 'deleted_at' => null]);
            if ($emailOwner) {
                throw new \RuntimeException('Email akun demo Fakultas sudah digunakan akun lain.');
            }
            $user = new User();
        } elseif ($user->role !== User::ROLE_FAKULTAS) {
            throw new \RuntimeException('Username demo Fakultas sudah digunakan role lain.');
        }

        $user->nama = 'Dr. Ir. Hendra Suardi';
        $user->email = $email;
        $user->username = $username;
        $user->role = User::ROLE_FAKULTAS;
        $user->fakultas_id = $faculty->fakultas_id;
        $user->setPassword('password123');
        if (!$user->save()) {
            throw new \RuntimeException(
                'Akun demo Fakultas gagal disimpan: ' . implode(' ', $user->getErrorSummary(true)),
            );
        }

        $this->stdout("Akun demo Fakultas Kedokteran siap.\nUsername: fk-demo\nPassword: password123\n");

        return ExitCode::OK;
    }
}
