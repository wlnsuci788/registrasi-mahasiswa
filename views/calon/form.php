<?php

use app\models\Pendaftaran;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var Pendaftaran $pendaftaran */
/** @var int $activeStep */
/** @var bool $readOnly */

$this->title = 'Formulir Registrasi';
$fieldOptions = ['class' => 'candidate-field'];
$this->registerCss(
    '.registration-dashboard .registration-campus-card{background-image:linear-gradient(0deg,rgba(0,0,0,.72),rgba(0,0,0,.08)),url("' .
    Url::to('@web/images/unand-campus-registration.png') .
    '");background-position:center;background-size:cover}',
    [],
    'registration-form-campus-image',
);
?>

<div class="cal-dashboard registration-dashboard">
    <?= $this->render('_steps', ['activeStep' => $activeStep, 'readOnly' => $readOnly]) ?>
    <div class="registration-grid">
        <div class="registration-main">
            <div class="registration-intro card-p">
                <div class="registration-seal" aria-hidden="true">&#10003;</div>
                <div>
                    <div class="eyebrow">FORMULIR REGISTRASI MAHASISWA INTERNASIONAL</div>
                    <h1>Data Diri</h1>
                    <p class="muted">Lengkapi informasi sesuai dokumen identitas resmi Anda.</p>
                </div>
                <span class="registration-status"><?= Html::encode($pendaftaran->status ?: Pendaftaran::STATUS_DRAFT) ?></span>
            </div>

            <?= Html::beginForm(['form'], 'post', ['class' => 'candidate-form']) ?>
            <?= $this->render('_readonly_notice', ['readOnly' => $readOnly]) ?>
            <?= Html::errorSummary($pendaftaran, ['class' => 'card-p form-errors']) ?>
            <fieldset class="registration-readonly-fields" <?= $readOnly ? 'disabled' : '' ?>>
            <section class="card-p registration-section">
                <div class="registration-section-heading">
                    <h2>1. Kategori Pendaftar</h2>
                    <span>Skema admisi internasional</span>
                </div>
                <div class="choice-cards">
                    <label class="choice-card <?= $pendaftaran->jalur === Pendaftaran::JALUR_ASING ? 'is-selected' : '' ?>">
                        <?= Html::activeRadio($pendaftaran, 'jalur', [
                            'value' => Pendaftaran::JALUR_ASING,
                            'uncheck' => null,
                            'id' => 'pendaftaran-jalur-asing',
                        ]) ?>
                        <span><strong>Warga Negara Asing (WNA)</strong><small>Bukan warga negara Indonesia. Untuk studi di UNAND, siapkan paspor negara asal.</small></span>
                    </label>
                    <label class="choice-card <?= $pendaftaran->jalur === Pendaftaran::JALUR_WNI ? 'is-selected' : '' ?>">
                        <?= Html::activeRadio($pendaftaran, 'jalur', [
                            'value' => Pendaftaran::JALUR_WNI,
                            'uncheck' => null,
                            'id' => 'pendaftaran-jalur-wni',
                        ]) ?>
                        <span><strong>Warga Negara Indonesia (WNI) di Luar Negeri</strong><small>Warga Indonesia yang tinggal di luar negeri; jalur credit earning / pertukaran pelajar.</small></span>
                    </label>
                </div>
                <?= Html::error($pendaftaran, 'jalur') ?>
            </section>

            <section class="card-p registration-section">
                <div class="registration-section-heading">
                    <h2>2. Data Identitas Pribadi</h2>
                    <span>Sesuai paspor resmi</span>
                </div>
                <div class="form-grid">
                    <div class="<?= $fieldOptions['class'] ?> form-field-wide">
                        <?= Html::activeLabel($pendaftaran, 'nama_lengkap', ['class' => 'form-label']) ?>
                        <?= Html::activeTextInput($pendaftaran, 'nama_lengkap', ['placeholder' => 'Nama lengkap sesuai paspor', 'autocomplete' => 'name', 'required' => true]) ?>
                        <?= Html::error($pendaftaran, 'nama_lengkap') ?>
                    </div>
                    <div class="<?= $fieldOptions['class'] ?>">
                        <?= Html::activeLabel($pendaftaran, 'tempat_lahir', ['class' => 'form-label']) ?>
                        <?= Html::activeTextInput($pendaftaran, 'tempat_lahir', ['placeholder' => 'Kota tempat lahir', 'required' => true]) ?>
                        <?= Html::error($pendaftaran, 'tempat_lahir') ?>
                    </div>
                    <div class="<?= $fieldOptions['class'] ?>">
                        <?= Html::activeLabel($pendaftaran, 'tanggal_lahir', ['class' => 'form-label']) ?>
                        <?= Html::activeInput('date', $pendaftaran, 'tanggal_lahir', ['required' => true]) ?>
                        <?= Html::error($pendaftaran, 'tanggal_lahir') ?>
                    </div>
                    <fieldset class="candidate-field form-field-wide registration-gender">
                        <legend><?= Html::encode($pendaftaran->getAttributeLabel('jenis_kelamin')) ?></legend>
                        <div class="choice-cards choice-cards-compact">
                            <?php foreach (Pendaftaran::optsJenisKelamin() as $value => $label): ?>
                                <label class="choice-card <?= $pendaftaran->jenis_kelamin === $value ? 'is-selected' : '' ?>">
                                    <?= Html::activeRadio($pendaftaran, 'jenis_kelamin', [
                                        'value' => $value,
                                        'label' => false,
                                        'uncheck' => null,
                                        'id' => 'pendaftaran-jenis-kelamin-' . ($value === Pendaftaran::JENIS_KELAMIN_LAKI_LAKI ? 'laki-laki' : 'perempuan'),
                                    ]) ?>
                                    <span><strong><?= Html::encode($label) ?></strong></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <?= Html::error($pendaftaran, 'jenis_kelamin') ?>
                    </fieldset>
                </div>
            </section>

            <section class="card-p registration-section">
                <div class="registration-section-heading">
                    <h2>3. Kewarganegaraan &amp; Paspor</h2>
                    <span>Gunakan dokumen perjalanan yang masih berlaku</span>
                </div>
                <div class="form-grid">
                    <div class="<?= $fieldOptions['class'] ?>">
                        <?= Html::activeLabel($pendaftaran, 'asal_negara', ['class' => 'form-label']) ?>
                        <?= Html::activeTextInput($pendaftaran, 'asal_negara', ['placeholder' => 'Negara asal / kewarganegaraan', 'required' => true]) ?>
                        <?= Html::error($pendaftaran, 'asal_negara') ?>
                    </div>
                    <div class="<?= $fieldOptions['class'] ?>">
                        <?= Html::activeLabel($pendaftaran, 'no_paspor', ['class' => 'form-label']) ?>
                        <?= Html::activeTextInput($pendaftaran, 'no_paspor', ['placeholder' => 'Nomor paspor']) ?>
                        <?= Html::error($pendaftaran, 'no_paspor') ?>
                    </div>
                </div>
            </section>

            </fieldset>
            <?php if (!$readOnly): ?>
            <div class="candidate-form-actions registration-actions">
                <?= Html::submitButton('Simpan Draft', ['class' => 'btn-secondary', 'name' => 'intent', 'value' => 'draft']) ?>
                <?= Html::submitButton('Lanjut: Tahap 2 (Program) →', ['class' => 'btn-primary', 'name' => 'intent', 'value' => 'next']) ?>
            </div>
            <?php else: ?>
                <div class="candidate-form-actions registration-actions">
                    <?= Html::a('← Kembali ke Beranda', ['index'], ['class' => 'btn-secondary']) ?>
                    <?= Html::a('Lanjut: Tahap 2 (Program) →', ['program'], ['class' => 'btn-primary']) ?>
                </div>
            <?php endif; ?>
            <?= Html::endForm() ?>
        </div>

        <aside class="registration-aside">
            <section class="registration-campus-card">
                <span>LIMAU MANIS</span>
                <strong>Universitas Andalas</strong>
                <small>Padang, Sumatera Barat</small>
            </section>
            <section class="card-p registration-help-card">
                <h2>Panduan Berkas Identitas</h2>
                <ul>
                    <li>Pastikan nama sama dengan paspor.</li>
                    <li>Isi negara asal sesuai kewarganegaraan.</li>
                    <li>Untuk pendaftar WNA, siapkan paspor yang masih berlaku.</li>
                </ul>
            </section>
            <section class="card-p registration-help-card visa-help-card">
                <h2>Layanan Visa Pelajar</h2>
                <p>Informasi dan pilihan bantuan visa tersedia pada Tahap 5 setelah data akademik dan dokumen.</p>
                <?= Html::a('Lihat Bantuan / Panduan', ['bantuan']) ?>
            </section>
        </aside>
    </div>
</div>
