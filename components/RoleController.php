<?php

namespace app\components;

use Yii;
use app\models\User;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Induk semua controller per aktor. Hanya role yang cocok yang boleh masuk.
 */
abstract class RoleController extends Controller
{
    abstract protected function allowedRole(): string;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            $identity = Yii::$app->user->identity;

                            return $identity instanceof User && $identity->role === $this->allowedRole();
                        },
                    ],
                ],
                'denyCallback' => function () {
                    $user = Yii::$app->user;
                    if ($user->isGuest) {
                        $user->loginRequired();
                        return;
                    }

                    $identity = $user->identity;
                    if (!$identity instanceof User) {
                        throw new \UnexpectedValueException('The authenticated identity is not a user account.');
                    }

                    Yii::$app->response->redirect(User::homeRoute($identity->role));
                },
            ],
        ];
    }
}
