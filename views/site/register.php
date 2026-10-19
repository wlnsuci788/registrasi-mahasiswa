<?php

use app\models\RegisterForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var RegisterForm $model */

$this->title = 'Buat Akun Portal';
$this->params['authPageClass'] = 'registration-account-page';
$this->params['authWrapClass'] = 'registration-account-wrap';
$this->registerCss(
    '.registration-account-campus{background-image:linear-gradient(0deg,rgba(0,0,0,.72),rgba(0,0,0,.08)),url("' .
    Url::to('@web/images/unand-campus-registration.png') .
    '");background-position:center;background-size:cover}',
    [],
    'registration-account-campus-image',
);
?>

<header class="public-portal-nav">
    <div class="public-portal-brand">
        <?= Html::img(Url::to('@web/images/unand-logo.png'), ['alt' => 'Logo Universitas Andalas']) ?>
        <span><strong>UNIVERSITAS ANDALAS</strong><small>INTERNATIONAL STUDENT PORTAL</small></span>
    </div>
</header>

<div class="registration-account-breadcrumb">
    <?= Html::a('Beranda', ['site/index']) ?>
    <span aria-hidden="true">›</span>
    <strong>Registrasi Akun Calon Mahasiswa</strong>
</div>

<div class="registration-account-heading">
    <span class="eyebrow">LANGKAH 1: BUAT AKUN</span>
    <strong>Portal Resmi UNAND</strong>
</div>

<div class="registration-account-grid">
    <section class="card-p registration-account-card">
        <h1>Buat Akun Portal Pendaftaran Mahasiswa Asing</h1>
        <p class="muted registration-account-lead">Buat akun terlebih dahulu. Setelah berhasil, Anda langsung masuk ke tahap Data Diri pendaftaran.</p>

        <?php $form = ActiveForm::begin([
            'id' => 'register-form',
            'fieldConfig' => [
                'options' => ['class' => 'field registration-account-field'],
                'template' => "{label}\n{input}\n{error}",
                'errorOptions' => ['class' => 'help-block'],
            ],
        ]); ?>

        <fieldset class="registration-category-field">
            <legend>Kategori calon mahasiswa <span class="req">*</span></legend>
            <div class="registration-category-options">
                <label class="registration-category-option <?= $model->jalur === RegisterForm::CATEGORY_WNA ? 'is-selected' : '' ?>">
                    <?= Html::activeRadio($model, 'jalur', [
                        'value' => RegisterForm::CATEGORY_WNA,
                        'label' => false,
                        'uncheck' => null,
                        'id' => 'registerform-jalur-wna',
                    ]) ?>
                    <span><strong>Warga Negara Asing (WNA)</strong><small>Bukan warga negara Indonesia; gunakan paspor negara asal.</small></span>
                </label>
                <label class="registration-category-option <?= $model->jalur === RegisterForm::CATEGORY_WNI ? 'is-selected' : '' ?>">
                    <?= Html::activeRadio($model, 'jalur', [
                        'value' => RegisterForm::CATEGORY_WNI,
                        'label' => false,
                        'uncheck' => null,
                        'id' => 'registerform-jalur-wni',
                    ]) ?>
                    <span><strong>Warga Negara Indonesia (WNI) di Luar Negeri</strong><small>Warga Indonesia yang tinggal di luar negeri; credit earning / pertukaran pelajar.</small></span>
                </label>
            </div>
            <?= Html::error($model, 'jalur', ['class' => 'help-block']) ?>
        </fieldset>

        <?= $form->field($model, 'nama')->textInput([
            'autofocus' => true,
            'autocomplete' => 'name',
            'placeholder' => 'Nama lengkap sesuai paspor / KTP',
        ]) ?>

        <?= $form->field($model, 'email')->input('email', [
            'autocomplete' => 'email',
            'placeholder' => 'Email aktif untuk masuk ke portal',
        ]) ?>

        <div class="registration-password-grid">
            <?= $form->field($model, 'password')->passwordInput([
                'autocomplete' => 'new-password',
                'placeholder' => 'Minimal 8 karakter',
            ]) ?>
            <?= $form->field($model, 'passwordConfirmation')->passwordInput([
                'autocomplete' => 'new-password',
                'placeholder' => 'Ulangi kata sandi',
            ]) ?>
        </div>
        <p class="registration-password-hint">Gunakan kata sandi minimal 8 karakter dan pastikan kedua kata sandi sama.</p>

        <div class="registration-consent">
            <?= Html::activeCheckbox($model, 'consent', [
                'value' => '1',
                'uncheck' => '0',
                'label' => 'Saya menyetujui ketentuan penggunaan Portal Pendaftaran Universitas Andalas.',
                'labelOptions' => ['class' => 'registration-consent-label'],
            ]) ?>
            <?= Html::error($model, 'consent', ['class' => 'help-block']) ?>
        </div>

        <?= Html::submitButton('Daftar Akun Sekarang →', ['class' => 'btn-main registration-submit']) ?>
        <?php ActiveForm::end(); ?>

        <div class="registration-account-login">
            Sudah punya akun?
            <?= Html::a('Masuk ke Portal', ['site/login']) ?>
        </div>
    </section>

    <aside class="registration-account-aside">
        <section class="registration-account-campus">
            <div><strong>Universitas Andalas</strong><span>Limau Manis, Padang · Sumatera Barat</span></div>
        </section>
        <section class="card-p registration-account-steps">
            <h2>6 Alur Pendaftaran</h2>
            <ol>
                <li class="is-current"><span>1</span><div><strong>Buat Akun &amp; Data Diri</strong><small>Aktif</small></div></li>
                <li><span>2</span><div>Pengisian Data Diri</div></li>
                <li><span>3</span><div>Pilihan Program Studi</div></li>
                <li><span>4</span><div>Unggah Berkas Akademik</div></li>
                <li><span>5</span><div>Bantuan Visa &amp; Izin Belajar</div></li>
                <li><span>6</span><div>Ringkasan &amp; Penerimaan</div></li>
            </ol>
        </section>
        <section class="card-p registration-account-benefits">
            <h2>Fasilitas Mahasiswa Internasional</h2>
            <p>✓ Asrama khusus mahasiswa asing</p>
            <p>✓ Program Bahasa Indonesia (BIPA)</p>
            <p>✓ Buddy student lokal &amp; riset perpustakaan</p>
        </section>
    </aside>
</div>

<footer class="registration-account-footer">
    <strong>Universitas Andalas</strong>
    <span>Kantor Pelayanan Internasional, Gedung Rektorat Lt. 2, Limau Manis, Padang</span>
    <small>© <?= date('Y') ?> Universitas Andalas. Hak Cipta Dilindungi.</small>
</footer>
