<?php

namespace app\controllers;

use app\components\RoleController;
use app\models\User;

class PdkController extends RoleController
{
    public $layout = 'staff';

    protected function allowedRole(): string
    {
        return User::ROLE_PDK;
    }

    public function actionIndex(): string
    {
        return $this->render('index');
    }
}
