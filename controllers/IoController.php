<?php

namespace app\controllers;

use app\components\RoleController;
use app\models\User;

class IoController extends RoleController
{
    public $layout = 'staff';

    protected function allowedRole(): string
    {
        return User::ROLE_IO;
    }

    public function actionIndex(): string
    {
        return $this->render('index');
    }
}
