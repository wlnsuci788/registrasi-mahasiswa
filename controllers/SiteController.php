<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\ContactForm;
use app\models\LoginForm;
use app\models\Pendaftaran;
use app\models\RegisterForm;
use app\models\User;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\base\Security;
use yii\mail\MailerInterface;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

class SiteController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly MailerInterface $mailer,
        private readonly Security $security,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'transparent' => true,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return Response|string
     */
    public function actionIndex(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            $identity = Yii::$app->user->identity;
            if (!$identity instanceof User) {
                throw new \UnexpectedValueException('The authenticated identity is not a user account.');
            }

            return $this->redirect(User::homeRoute($identity->role));
        }

        return $this->redirect(['site/login']);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'auth';
        $model = new LoginForm($this->security);

        if ($model->load($this->request->post()) && $model->login()) {
            $user = $model->getUser();
            if (!$user) {
                throw new \LogicException('A successful login must have a user account.');
            }

            return $this->redirect(User::homeRoute($user->role));
        }

        $model->password = '';

        return $this->render('login', ['model' => $model]);
    }

    public function actionRegister(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'auth';
        $model = new RegisterForm();
        if ($model->load($this->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $user = new User();
                $user->nama = trim($model->nama);
                $user->email = mb_strtolower(trim($model->email));
                $user->username = $this->makeUniqueUsername($user->email);
                $user->role = User::ROLE_CALON;
                $user->setPassword($model->password);
                if (!$user->save()) {
                    throw new \RuntimeException('Akun gagal dibuat: ' . implode(' ', $user->getErrorSummary(true)));
                }

                $pendaftaran = new Pendaftaran();
                $pendaftaran->user_id = (int) $user->user_id;
                $pendaftaran->nama_lengkap = trim($model->nama);
                $pendaftaran->jalur = $model->jalur;
                $pendaftaran->asal_negara = $model->jalur === RegisterForm::CATEGORY_WNI ? 'Indonesia' : null;
                $pendaftaran->status = Pendaftaran::STATUS_DRAFT;
                $pendaftaran->updated_by = (int) $user->user_id;
                if (!$pendaftaran->save()) {
                    throw new \RuntimeException(
                        'Draft pendaftaran gagal dibuat: ' . implode(' ', $pendaftaran->getErrorSummary(true)),
                    );
                }

                $transaction->commit();
            } catch (\Throwable $exception) {
                if ($transaction->getIsActive()) {
                    $transaction->rollBack();
                }
                throw $exception;
            }

            if (!Yii::$app->user->login($user, 0)) {
                throw new \RuntimeException('Akun berhasil dibuat, tetapi sesi login otomatis gagal.');
            }
            Yii::$app->session->setFlash('success', 'Akun berhasil dibuat. Silakan lanjutkan data diri pendaftaran.');

            return $this->redirect(['/calon/form']);
        }

        return $this->render('register', ['model' => $model]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();
        Yii::$app->session->setFlash('success', 'Anda telah berhasil keluar dari Portal UNAND.');

        return $this->redirect(['site/login']);
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact(): Response|string
    {
        $model = new ContactForm();

        $contact = $model->load($this->request->post()) && $model->contact(
            $this->mailer,
            Yii::$app->params['adminEmail'],
            Yii::$app->params['senderEmail'],
            Yii::$app->params['senderName'],
        );

        if ($contact) {
            Yii::$app->session->setFlash(
                'success',
                'Thank you for contacting us. We will respond to you as soon as possible.',
            );

            return $this->refresh();
        }

        return $this->render('contact', ['model' => $model]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout(): string
    {
        return $this->render('about');
    }

    private function makeUniqueUsername(string $email): string
    {
        $localPart = strstr($email, '@', true);
        $base = strtolower((string) preg_replace('/[^a-z0-9._-]+/', '', $localPart ?: 'calon'));
        $base = substr($base ?: 'calon', 0, 40);
        $username = $base;
        $suffix = 1;

        while (User::find()->where(['username' => $username])->exists()) {
            $username = substr($base, 0, 40 - strlen((string) $suffix)) . $suffix;
            $suffix++;
        }

        return $username;
    }
}
