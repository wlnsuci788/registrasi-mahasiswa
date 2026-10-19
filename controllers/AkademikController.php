<?php

namespace app\controllers;

use app\components\RoleController;
use app\models\User;

class AkademikController extends RoleController
{
    public $layout = 'staff';

    protected function allowedRole(): string
    {
        return User::ROLE_AKADEMIK;
    }

    public function actionIndex(): string
    {
        return $this->render('index');
    }
}
