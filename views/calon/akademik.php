<?php

use app\models\Pendaftaran;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Pendaftaran $pendaftaran */
/** @var int $activeStep */
/** @var bool $readOnly */

$this->title = 'Data Akademik';
?>

<div class="cal-dashboard registration-dashboard">
    <?= $this->render('_steps', ['activeStep' => $activeStep, 'readOnly' => $readOnly]) ?>
    <div class="registration-grid">
        <div class="registration-main">
    <div class="cal-greeting">
        <div>
            <div class="eyebrow">TAHAP 3 DARI 6</div>
            <h1>Riwayat Akademik</h1>
            <p class="muted">Lengkapi informasi institusi asal, nilai akademik, dan sumber pembiayaan.</p>
        </div>
    </div>

    <?= Html::beginForm(['akademik'], 'post', ['class' => 'candidate-form']) ?>
    <?= $this->render('_readonly_notice', ['readOnly' => $readOnly]) ?>
    <?= Html::errorSummary($pendaftaran, ['class' => 'card-p form-errors']) ?>
    <fieldset class="registration-readonly-fields" <?= $readOnly ? 'disabled' : '' ?>>
    <section class="card-p">
        <div class="form-grid">
            <div class="candidate-field">
                <?= Html::activeLabel($pendaftaran, 'asal_institusi', ['class' => 'form-label']) ?>
                <?= Html::activeTextInput($pendaftaran, 'asal_institusi', ['placeholder' => 'Sekolah / universitas asal']) ?>
                <?= Html::error($pendaftaran, 'asal_institusi') ?>
            </div>
            <div class="candidate-field">
                <?= Html::activeLabel($pendaftaran, 'nilai_akhir', ['class' => 'form-label']) ?>
                <?= Html::activeInput('number', $pendaftaran, 'nilai_akhir', ['min' => 0, 'max' => 100, 'step' => '0.01', 'placeholder' => 'Contoh: 3.75 atau 85']) ?>
                <?= Html::error($pendaftaran, 'nilai_akhir') ?>
            </div>
            <div class="candidate-field">
                <?= Html::activeLabel($pendaftaran, 'pembiayaan', ['class' => 'form-label']) ?>
                <?= Html::activeDropDownList($pendaftaran, 'pembiayaan', Pendaftaran::optsPembiayaan(), ['prompt' => 'Pilih sumber pembiayaan']) ?>
                <?= Html::error($pendaftaran, 'pembiayaan') ?>
            </div>
            <?php if ($pendaftaran->kategori_program === Pendaftaran::KATEGORI_PROGRAM_NON_DEGREE): ?>
                <div class="candidate-field">
                    <?= Html::activeLabel($pendaftaran, 'lama_kunjungan', ['class' => 'form-label']) ?>
                    <?= Html::activeTextInput($pendaftaran, 'lama_kunjungan', ['placeholder' => 'Contoh: 1 semester']) ?>
                    <?= Html::error($pendaftaran, 'lama_kunjungan') ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    </fieldset>
    <?php if (!$readOnly): ?>
    <div class="candidate-form-actions">
        <?= Html::a('← Kembali ke Pilihan Program', ['program'], ['class' => 'btn-secondary']) ?>
        <?= Html::submitButton('Simpan &amp; Lanjut ke Dokumen →', ['class' => 'btn-primary', 'encode' => false]) ?>
    </div>
    <?php else: ?>
        <div class="candidate-form-actions registration-actions">
            <?= Html::a('← Kembali: Tahap 2 (Program)', ['program'], ['class' => 'btn-secondary']) ?>
            <?= Html::a('Lanjut: Tahap 4 (Unggah Dokumen) →', ['dokumen'], ['class' => 'btn-primary']) ?>
        </div>
    <?php endif; ?>
    <?= Html::endForm() ?>
        </div>
        <?= $this->render('_registration_aside', ['pendaftaran' => $pendaftaran, 'activeStep' => $activeStep]) ?>
    </div>
</div>
