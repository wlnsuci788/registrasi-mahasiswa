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
$menu = [
    ['Beranda', '/calon/index'],
    ['Formulir Registrasi', '/calon/form'],
    ['Status Pendaftaran', '/calon/status'],
    ['Bantuan / Panduan', '/calon/bantuan'],
];
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <title><?= Html::encode($this->title) ?> | Portal Mahasiswa Asing UNAND</title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<header class="cal-nav">
    <div class="cal-brand">
        <?= Html::img(Url::to('@web/images/unand-logo.png'), [
            'alt' => 'Logo Universitas Andalas',
            'class' => 'cal-logo',
        ]) ?>
        <div>
            <b>UNIVERSITAS ANDALAS</b>
            <small>INTERNATIONAL STUDENT PORTAL</small>
        </div>
    </div>

    <nav class="cal-menu">
        <?php foreach ($menu as [$label, $url]): ?>
            <?php
            $isActive = match ($label) {
                'Beranda' => $route === 'calon/index',
                'Formulir Registrasi' => in_array($route, ['calon/form', 'calon/program', 'calon/akademik', 'calon/dokumen', 'calon/visa', 'calon/ringkasan'], true),
                'Status Pendaftaran' => $route === 'calon/status',
                default => ltrim($url, '/') === $route,
            };
            ?>
            <a href="<?= Html::encode(Url::to([$url])) ?>"
               class="<?= $isActive ? 'on' : '' ?>"><?= Html::encode($label) ?></a>
        <?php endforeach; ?>
    </nav>

    <div class="cal-right">
        <span class="avatar" title="<?= Html::encode($user->nama) ?>"><?= Html::encode($user->inisial) ?></span>
        <?= Html::beginForm(['/site/logout'], 'post', [
            'onsubmit' => "return confirm('Apakah Anda yakin ingin keluar?');",
        ]) ?>
        <button type="submit" class="logout-btn">Keluar</button>
        <?= Html::endForm() ?>
    </div>
</header>

<main class="cal-main">
    <?= Alert::widget() ?>
    <?= $content ?>
</main>

<footer class="cal-foot">
    <div><b>Universitas Andalas</b>Kantor Pelayanan Internasional (International Office), Gedung Rektorat Lt. 2, Limau Manis, Padang</div>
    <div>&copy; <?= date('Y') ?> Universitas Andalas. Hak Cipta Dilindungi.<br>Sistem Penerimaan Mahasiswa Asing (International Admissions)</div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
