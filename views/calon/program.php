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
            <div class="cal-greeting program-page-heading">
                <div>
                    <div class="eyebrow">KATEGORI APLIKASI</div>
                    <h1>Pilih Kategori Program Studi</h1>
                    <p class="muted">Silakan pilih jalur program yang sesuai dengan tujuan akademik Anda. Jalur Degree diperuntukkan bagi mahasiswa yang ingin menyelesaikan kurikulum komprehensif hingga wisuda, sedangkan jalur Non-Degree diperuntukkan untuk pertukaran pelajar atau aktivitas singkat.</p>
                </div>
                <span class="program-intake-badge">Intake 2026/2027</span>
            </div>

            <?= Html::beginForm(['program'], 'post', ['class' => 'candidate-form program-form']) ?>
            <?= $this->render('_readonly_notice', ['readOnly' => $readOnly]) ?>
            <?= Html::errorSummary($pendaftaran, ['class' => 'card-p form-errors']) ?>
            <fieldset class="registration-readonly-fields" <?= $readOnly ? 'disabled' : '' ?>>
                <section class="card-p program-section">
                    <div class="program-section-heading">
                        <div>
                            <span class="eyebrow">PILIH JALUR STUDI</span>
                            <h2>Kategori Program <span aria-hidden="true">*</span></h2>
                        </div>
                        <span class="program-academic-year">Pendaftaran Mahasiswa Internasional</span>
                    </div>
                    <div class="program-category-grid" role="radiogroup" aria-label="Kategori program">
                        <label class="program-category-card <?= $pendaftaran->kategori_program === Pendaftaran::KATEGORI_PROGRAM_DEGREE ? 'is-selected' : '' ?>">
                            <?= Html::activeRadio($pendaftaran, 'kategori_program', [
                                'value' => Pendaftaran::KATEGORI_PROGRAM_DEGREE,
                                'id' => 'pendaftaran-kategori-degree',
                                'class' => 'program-category-radio',
                                'label' => false,
                                'uncheck' => false,
                            ]) ?>
                            <span class="program-category-indicator" aria-hidden="true">
                                <?= $pendaftaran->kategori_program === Pendaftaran::KATEGORI_PROGRAM_DEGREE ? '✓ Dipilih' : 'Pilih' ?>
                            </span>
                            <span class="program-category-icon" aria-hidden="true">▤</span>
                            <span class="program-category-copy">
                                <strong>Degree (Sarjana/Pascasarjana)</strong>
                                <small>Untuk program studi bergelar dengan pilihan jenjang S1, S2, atau S3.</small>
                                <span class="program-category-tags">S1 · S2 · S3</span>
                            </span>
                        </label>
                        <label class="program-category-card <?= $pendaftaran->kategori_program === Pendaftaran::KATEGORI_PROGRAM_NON_DEGREE ? 'is-selected' : '' ?>">
                            <?= Html::activeRadio($pendaftaran, 'kategori_program', [
                                'value' => Pendaftaran::KATEGORI_PROGRAM_NON_DEGREE,
                                'id' => 'pendaftaran-kategori-non-degree',
                                'class' => 'program-category-radio',
                                'label' => false,
                                'uncheck' => false,
                            ]) ?>
                            <span class="program-category-indicator" aria-hidden="true">
                                <?= $pendaftaran->kategori_program === Pendaftaran::KATEGORI_PROGRAM_NON_DEGREE ? '✓ Dipilih' : 'Pilih' ?>
                            </span>
                            <span class="program-category-icon program-category-icon-alt" aria-hidden="true">◉</span>
                            <span class="program-category-copy">
                                <strong>Non-Degree (Program Jangka Pendek)</strong>
                                <small>Untuk pertukaran pelajar, credit earning, dan program singkat lainnya.</small>
                                <span class="program-category-tags">Student Exchange · Short Course</span>
                            </span>
                        </label>
                    </div>
                    <?= Html::error($pendaftaran, 'kategori_program') ?>
                </section>

                <section class="card-p program-section">
                    <div class="program-section-heading program-detail-heading">
                        <div>
                            <span class="eyebrow">DETAIL PILIHAN</span>
                            <h2>Lengkapi Pilihan Program</h2>
                        </div>
                        <span class="program-required-note">* Wajib diisi</span>
                    </div>
                    <div class="form-grid">
                        <div class="candidate-field degree-field">
                            <?= Html::activeLabel($pendaftaran, 'jenjang', ['class' => 'form-label']) ?>
                            <?= Html::activeDropDownList($pendaftaran, 'jenjang', Pendaftaran::optsJenjang(), ['prompt' => 'Pilih jenjang pendidikan']) ?>
                            <?= Html::error($pendaftaran, 'jenjang') ?>
                        </div>
                        <div class="candidate-field degree-field">
                            <?= Html::activeLabel($pendaftaran, 'fakultas_id', ['class' => 'form-label']) ?>
                            <?= Html::activeDropDownList($pendaftaran, 'fakultas_id', $fakultasOptions, ['prompt' => 'Pilih fakultas tujuan']) ?>
                            <?= Html::error($pendaftaran, 'fakultas_id') ?>
                        </div>
                        <div class="candidate-field non-degree-field">
                            <?= Html::activeLabel($pendaftaran, 'jenis_program', ['class' => 'form-label']) ?>
                            <?= Html::activeDropDownList($pendaftaran, 'jenis_program', Pendaftaran::optsJenisProgram(), ['prompt' => 'Pilih jenis program']) ?>
                            <?= Html::error($pendaftaran, 'jenis_program') ?>
                        </div>
                    </div>
                    <p class="muted program-hint">Pilihan jenjang dan fakultas tersedia untuk program Degree. Untuk Non-Degree, pilih jenis program yang akan diikuti.</p>
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
const categories = document.querySelectorAll('.program-category-radio');
const updateProgramFields = function () {
    let selectedCategory = '';
    categories.forEach(function (input) {
        const card = input.closest('.program-category-card');
        card.classList.toggle('is-selected', input.checked);
        card.querySelector('.program-category-indicator').textContent = input.checked ? '✓ Dipilih' : 'Pilih';
        if (input.checked) {
            selectedCategory = input.value;
        }
    });
    document.querySelectorAll('.degree-field').forEach(function (field) {
        field.hidden = selectedCategory !== 'degree';
        field.querySelectorAll('input, select').forEach(function (input) {
            input.disabled = selectedCategory !== 'degree';
        });
    });
    document.querySelectorAll('.non-degree-field').forEach(function (field) {
        field.hidden = selectedCategory !== 'non-degree';
        field.querySelectorAll('input, select').forEach(function (input) {
            input.disabled = selectedCategory !== 'non-degree';
        });
    });
};
categories.forEach(function (input) {
    input.addEventListener('change', updateProgramFields);
});
updateProgramFields();
JS); ?>
