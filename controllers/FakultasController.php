<?php

namespace app\controllers;

use app\components\RoleController;
use app\models\User;

class FakultasController extends RoleController
{
    public $layout = 'staff';

    protected function allowedRole(): string
    {
        return User::ROLE_FAKULTAS;
    }

    public function actionIndex(): string
    {
        return $this->render('index');
    }
}
