<?php

use app\assets\AppAsset;
use app\models\User;
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
        ['Ringkasan', '/io/index', ['io/index']],
        ['Verifikasi Pendaftaran', null, []],
        ['Bantuan Visa', null, []],
        ['Kelola Dokumen Persyaratan', null, []],
    ]],
    User::ROLE_FAKULTAS => ['FAKULTAS', [
        ['Dashboard', '/fakultas/index', ['fakultas/index'], null, ['section' => null], '▦'],
        ['Fakultas', '/fakultas/evaluasi', ['fakultas/evaluasi'], null, ['id' => '891'], '☷'],
    ]],
    User::ROLE_PDK => ['MENU UPT PDK', [
        ['Seleksi Pendaftar WNI', '/pdk/index', ['pdk/index']],
    ]],
    User::ROLE_AKADEMIK => ['MENU AKADEMIK', [
        ['Registration Number', '/akademik/index', ['akademik/index']],
    ]],
];
[$menuLabel, $items] = $menus[$user->role] ?? ['MENU', []];

$peran = 'Petugas ' . $user->role;
if ($user->role === User::ROLE_FAKULTAS && $user->fakultas) {
    $peran .= ' - ' . $user->fakultas->nama_fakultas;
}
$facultyLayout = $user->role === User::ROLE_FAKULTAS;
if ($facultyLayout && $route === 'fakultas/evaluasi') {
    $menuLabel = 'FAKULTAS & PRODI';
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

<div class="staff<?= $facultyLayout ? ' staff-faculty' : '' ?>">
    <aside class="side">
        <div class="side-brand">
            <?= Html::img(Url::to('@web/images/unand-logo.png'), [
                'alt' => 'Logo Universitas Andalas',
                'class' => 'staff-logo',
            ]) ?>
            <div>
                <b>UNAND PORTAL</b>
                <small><?= $facultyLayout ? 'PANEL FAKULTAS KEDOKTERAN' : 'STAF &amp; VERIFIKATOR' ?></small>
            </div>
        </div>

        <?php if ($facultyLayout): ?>
            <div class="faculty-sidebar-year"><strong>Tahun Akademik</strong><b>2026/2027</b></div>
        <?php endif; ?>
        <div class="side-label"><?= Html::encode($menuLabel) ?></div>
        <nav>
            <?php foreach ($items as $item): ?>
                <?php
                [$label, $url, $activeRoutes] = array_slice($item, 0, 3);
                $fragment = $item[3] ?? null;
                $activeQuery = $item[4] ?? [];
                $icon = $item[5] ?? null;
                $urlOptions = [$url];
                foreach ($activeQuery as $key => $value) {
                    if ($value !== null) {
                        $urlOptions[$key] = $value;
                    }
                }
                $isActive = in_array($route, $activeRoutes, true);
                foreach ($activeQuery as $key => $value) {
                    if ($route === ltrim($url, '/')) {
                        $isActive = $isActive && Yii::$app->request->get($key) === $value;
                    }
                }
                ?>
                <?php if ($url): ?>
                    <a href="<?= Html::encode(Url::to($urlOptions) . ($fragment ? '#' . $fragment : '')) ?>"
                       class="<?= $isActive ? 'on' : '' ?>"><?php if ($icon):
                            ?><span class="faculty-nav-icon" aria-hidden="true"><?= Html::encode($icon) ?></span><?php
                              endif; ?><?= Html::encode($label) ?></a>
                <?php else: ?>
                    <a href="#" class="dis" title="Halaman menyusul"><?= Html::encode($label) ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>

        <?php if ($facultyLayout): ?>
            <div class="faculty-sidebar-footer">
                <span><i aria-hidden="true">◉</i><strong>Helpdesk SIA UNAND</strong><small>ext. 204 / BAK</small></span>
                <?= Html::beginForm(['/site/logout'], 'post', [
                    'onsubmit' => "return confirm('Apakah Anda yakin ingin keluar?');",
                ]) ?>
                <button type="submit" class="faculty-logout-icon" aria-label="Keluar" title="Keluar">◈</button>
                <?= Html::endForm() ?>
            </div>
        <?php else: ?>
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
        <?php endif; ?>
    </aside>

    <div class="stf-main">
        <header class="stf-top">
            <?php if ($facultyLayout): ?>
                <?= Html::beginForm(['/fakultas/index'], 'get', ['class' => 'faculty-global-search']) ?>
                <label>
                    <span aria-hidden="true">⌕</span>
                    <?= Html::textInput('q', Yii::$app->request->get('q', ''), [
                        'placeholder' => $route === 'fakultas/evaluasi'
                            ? 'Cari paspor, nama calon mahasiswa, prodi...'
                            : 'Cari nomor pendaftaran, paspor, atau nama...',
                        'aria-label' => 'Cari pendaftar Fakultas',
                    ]) ?>
                </label>
                <?= Html::endForm() ?>
                <div class="faculty-top-actions" aria-label="Informasi akun">
                    <span aria-hidden="true" title="Notifikasi">♧</span>
                    <span aria-hidden="true" title="Bantuan">ⓘ</span>
                    <div class="faculty-top-user">
                        <div>
                            <b><?= Html::encode($user->nama) ?></b>
                            <small><?= Html::encode($peran) ?></small>
                        </div>
                        <span class="avatar"><?= Html::encode($user->inisial) ?></span>
                    </div>
                </div>
            <?php else: ?>
                <span class="ta">&#9679; TAHUN AKADEMIK 2026/2027</span>
                <div class="me">
                    <div>
                        <b><?= Html::encode($user->nama) ?></b>
                        <small><?= Html::encode($peran) ?></small>
                    </div>
                    <span class="avatar"><?= Html::encode($user->inisial) ?></span>
                </div>
            <?php endif; ?>
        </header>
        <div class="stf-body">
            <?= $content ?>
        </div>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
