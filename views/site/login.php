<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

$this->title = 'Masuk';

$this->registerJs(<<<'JS'
document.querySelectorAll('.pw-row button').forEach(function (button) {
    button.addEventListener('click', function () {
        var input = button.parentNode.querySelector('input');
        input.type = input.type === 'password' ? 'text' : 'password';
        button.textContent = input.type === 'password' ? 'Tampilkan' : 'Sembunyikan';
    });
});
JS);
?>

<div class="hero-green">
    <span class="logo-box">
        <?= Html::img(Url::to('@web/images/unand-logo.png'), [
            'alt' => 'Lambang Universitas Andalas',
            'class' => 'unand-logo',
        ]) ?>
    </span>
    <div class="hero-copy">
        <span class="pill">TAHUN AKADEMIK 2026/2027</span>
        <h1>Portal Mahasiswa Asing</h1>
        <small>UNAND International Student Admissions &amp; Mobility</small>
    </div>
</div>

<div class="notice">
    <span>GELOMBANG 2 DIBUKA</span>
    <span class="d">s/d 30 Juni 2025</span>
</div>

<div class="auth-card">
    <div class="tabs">
        <a href="<?= Url::to(['site/login']) ?>" class="on">Masuk / Sign In</a>
        <a href="<?= Url::to(['site/register']) ?>">Daftar / Sign Up</a>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'fieldConfig' => [
            'options' => ['class' => 'field'],
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'help-block'],
        ],
    ]); ?>

    <?= $form->field($model, 'username')
        ->textInput([
            'autofocus' => true,
            'autocomplete' => 'username',
            'placeholder' => 'student@unand.ac.id atau username',
        ])
        ->label('Email atau Username <span class="req">*</span>', ['encode' => false]) ?>

    <?= $form->field($model, 'password', [
        'template' => "{label}\n<div class=\"pw-row\">{input}<button type=\"button\" aria-label=\"Tampilkan kata sandi\">Tampilkan</button></div>\n{error}",
    ])->passwordInput([
        'autocomplete' => 'current-password',
        'placeholder' => '••••••••',
    ])->label('Kata Sandi <span class="req">*</span>', ['encode' => false]) ?>

    <?= Html::submitButton('Masuk ke Portal &rarr;', [
        'class' => 'btn-main',
        'encode' => false,
    ]) ?>

    <?php ActiveForm::end(); ?>

    <div class="staff-login">
        <a href="#login-form">Masuk sebagai petugas</a>
    </div>

    <div class="dummy">
        Akun demo Biro Akademik: <code>akademik</code> &middot; Kata sandi: <code>password123</code>
        <small>Akun demo tersedia setelah data dummy dimuat ke database.</small>
    </div>

    <div class="auth-links">
        Belum punya akun?
        <a href="<?= Url::to(['site/register']) ?>"><b>Registrasi Sekarang &rarr;</b></a>
    </div>
</div>

<div class="contact-card">
    <b>Kantor Urusan Internasional (KUI)</b>
    <div class="contact-details">
        <span>io@unand.ac.id</span>
        <span>+62 751 71181</span>
        <span>08.00-16.00</span>
    </div>
</div>

<div class="auth-foot">
    &copy; <?= date('Y') ?> Universitas Andalas. Kedjajaan Bangsa.<br>
    Direktorat Teknologi Informasi &amp; Komunikasi (DTIK) UNAND
</div>
