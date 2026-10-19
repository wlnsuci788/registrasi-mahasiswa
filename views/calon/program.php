<?php

use app\models\Pendaftaran;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Pendaftaran $pendaftaran */
/** @var array<int, string> $fakultasOptions */
/** @var int $activeStep */
/** @var bool $readOnly */

$this->title = 'Pilihan Program';
?>

<div class="cal-dashboard registration-dashboard">
    <?= $this->render('_steps', ['activeStep' => $activeStep, 'readOnly' => $readOnly]) ?>
    <div class="registration-grid">
        <div class="registration-main">
    <div class="cal-greeting">
        <div>
            <div class="eyebrow">TAHAP 2 DARI 6</div>
            <h1>Pilih Program Studi</h1>
            <p class="muted">Pilih kategori program dan program studi yang sesuai dengan rencana Anda.</p>
        </div>
    </div>

    <?= Html::beginForm(['program'], 'post', ['class' => 'candidate-form']) ?>
    <?= $this->render('_readonly_notice', ['readOnly' => $readOnly]) ?>
    <?= Html::errorSummary($pendaftaran, ['class' => 'card-p form-errors']) ?>
    <fieldset class="registration-readonly-fields" <?= $readOnly ? 'disabled' : '' ?>>
    <section class="card-p">
        <div class="form-grid">
            <div class="candidate-field form-field-wide">
                <?= Html::activeLabel($pendaftaran, 'kategori_program', ['class' => 'form-label']) ?>
                <?= Html::activeDropDownList($pendaftaran, 'kategori_program', Pendaftaran::optsKategoriProgram(), [
                    'prompt' => 'Pilih kategori program',
                    'id' => 'pendaftaran-kategori_program',
                ]) ?>
                <?= Html::error($pendaftaran, 'kategori_program') ?>
            </div>
            <div class="candidate-field degree-field">
                <?= Html::activeLabel($pendaftaran, 'jenjang', ['class' => 'form-label']) ?>
                <?= Html::activeDropDownList($pendaftaran, 'jenjang', Pendaftaran::optsJenjang(), ['prompt' => 'Pilih jenjang']) ?>
                <?= Html::error($pendaftaran, 'jenjang') ?>
            </div>
            <div class="candidate-field degree-field">
                <?= Html::activeLabel($pendaftaran, 'fakultas_id', ['class' => 'form-label']) ?>
                <?= Html::activeDropDownList($pendaftaran, 'fakultas_id', $fakultasOptions, ['prompt' => 'Pilih fakultas']) ?>
                <?= Html::error($pendaftaran, 'fakultas_id') ?>
            </div>
            <div class="candidate-field non-degree-field">
                <?= Html::activeLabel($pendaftaran, 'jenis_program', ['class' => 'form-label']) ?>
                <?= Html::activeDropDownList($pendaftaran, 'jenis_program', Pendaftaran::optsJenisProgram(), ['prompt' => 'Pilih jenis program']) ?>
                <?= Html::error($pendaftaran, 'jenis_program') ?>
            </div>
        </div>
        <p class="muted program-hint">Degree: pilih jenjang dan fakultas tujuan. Non-degree: pilih jenis program, misalnya credit earning, student exchange, atau short course.</p>
    </section>
    </fieldset>
    <?php if (!$readOnly): ?>
    <div class="candidate-form-actions">
        <?= Html::a('← Kembali ke Data Diri', ['form'], ['class' => 'btn-secondary']) ?>
        <?= Html::submitButton('Simpan Draft', ['class' => 'btn-secondary', 'name' => 'intent', 'value' => 'draft']) ?>
        <?= Html::submitButton('Simpan &amp; Lanjut ke Akademik →', ['class' => 'btn-primary', 'name' => 'intent', 'value' => 'next', 'encode' => false]) ?>
    </div>
    <?php else: ?>
        <div class="candidate-form-actions registration-actions">
            <?= Html::a('← Kembali: Tahap 1 (Data Diri)', ['form'], ['class' => 'btn-secondary']) ?>
            <?= Html::a('Lanjut: Tahap 3 (Data Akademik) →', ['akademik'], ['class' => 'btn-primary']) ?>
        </div>
    <?php endif; ?>
    <?= Html::endForm() ?>
        </div>
        <?= $this->render('_registration_aside', ['pendaftaran' => $pendaftaran, 'activeStep' => $activeStep]) ?>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
const category = document.getElementById('pendaftaran-kategori_program');
const updateProgramFields = function () {
    document.querySelectorAll('.degree-field').forEach(function (field) {
        field.hidden = category.value !== 'degree';
    });
    document.querySelectorAll('.non-degree-field').forEach(function (field) {
        field.hidden = category.value !== 'non-degree';
    });
};
category.addEventListener('change', updateProgramFields);
updateProgramFields();
JS); ?>
