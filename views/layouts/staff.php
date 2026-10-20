<?php

use app\assets\AppAsset;
use app\models\User;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var string $content */
/** @var User $user */

AppAsset::register($this);
$this->registerCsrfMetaTags();
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);

$identity = Yii::$app->user->identity;
if (!$identity instanceof User) {
    throw new \UnexpectedValueException('The authenticated identity is not a user account.');
}
$user = $identity;
$route = Yii::$app->controller->route;
$menus = [
    User::ROLE_IO => ['MENU PETUGAS IO', [
        ['Ringkasan', '/io/index'],
        ['Verifikasi Pendaftaran', null],
        ['Bantuan Visa', null],
        ['Kelola Dokumen Persyaratan', null],
    ]],
    User::ROLE_FAKULTAS => ['MENU FAKULTAS', [
        ['Saran Fakultas', '/fakultas/index'],
    ]],
    User::ROLE_PDK => ['MENU UPT PDK', [
        ['Dashboard', '/pdk/index'],
        ['Pendidikan Luar Kampus', '/pdk/pendidikan'],
        ['BIPA & Credit Earning', '/pdk/bipa'],
        ['Riwayat Seleksi', '/pdk/riwayat'],
    ]],
    User::ROLE_AKADEMIK => ['MENU AKADEMIK', [
        ['Registration Number', '/akademik/index'],
    ]],
];
[$menuLabel, $items] = $menus[$user->role] ?? ['MENU', []];

$peran = 'Petugas ' . $user->role;
if ($user->role === User::ROLE_FAKULTAS && $user->fakultas) {
    $peran .= ' - ' . $user->fakultas->nama_fakultas;
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <title><?= Html::encode($this->title) ?> | UNAND Portal</title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="staff<?= $user->role === User::ROLE_PDK ? ' staff-pdk' : '' ?>">
    <aside class="side">
        <div class="side-brand">
            <?= Html::img(Url::to('@web/images/unand-logo.png'), [
                'alt' => 'Logo Universitas Andalas',
                'class' => 'staff-logo',
            ]) ?>
            <div>
                <b>UNAND PORTAL</b>
                <small>STAF &amp; VERIFIKATOR</small>
            </div>
        </div>

        <div class="side-label"><?= Html::encode($menuLabel) ?></div>
        <nav>
            <?php foreach ($items as [$label, $url]): ?>
                <?php if ($url): ?>
                    <?php $isActive = ltrim($url, '/') === $route || ($url === '/pdk/pendidikan' && $route === 'pdk/evaluasi'); ?>
                    <a href="<?= Html::encode(Url::to([$url])) ?>"
                       class="<?= $isActive ? 'on' : '' ?>"
                       <?= $isActive ? 'aria-current="page"' : '' ?>><?= Html::encode($label) ?></a>
                <?php else: ?>
                    <a href="#" class="dis" title="Halaman menyusul"><?= Html::encode($label) ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>

        <div class="side-user">
            <span class="avatar"><?= Html::encode($user->inisial) ?></span>
            <div>
                <div class="nm"><?= Html::encode($user->nama) ?></div>
                <div class="rl"><?= Html::encode($peran) ?></div>
            </div>
            <?= Html::beginForm(['/site/logout'], 'post', [
                'onsubmit' => "return confirm('Apakah Anda yakin ingin keluar?');",
            ]) ?>
            <button type="submit" class="logout-btn">Keluar</button>
            <?= Html::endForm() ?>
        </div>
    </aside>

    <div class="stf-main">
        <header class="stf-top">
            <span class="ta">&#9679; TAHUN AKADEMIK 2026/2027</span>
            <div class="me">
                <div>
                    <b><?= Html::encode($user->nama) ?></b>
                    <small><?= Html::encode($peran) ?></small>
                </div>
                <span class="avatar"><?= Html::encode($user->inisial) ?></span>
            </div>
        </header>
        <div class="stf-body">
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
