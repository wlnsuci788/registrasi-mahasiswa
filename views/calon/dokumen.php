<?php

use app\models\DokumenPendaftar;
use app\models\DokumenPersyaratan;
use app\models\Pendaftaran;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Pendaftaran $pendaftaran */
/** @var DokumenPersyaratan[] $requirements */
/** @var DokumenPendaftar[] $uploaded */
/** @var int $activeStep */
/** @var bool $readOnly */

$this->title = 'Unggah Dokumen';
$uploadedByRequirement = [];
foreach ($uploaded as $file) {
    $uploadedByRequirement[(int) $file->dokumen_id] = $file;
}
$uploadedCount = 0;
foreach ($requirements as $requirement) {
    if (isset($uploadedByRequirement[(int) $requirement->dokumen_id])) {
        $uploadedCount++;
    }
}
$requirementCount = count($requirements);
$completionPercent = $requirementCount > 0 ? (int) round(($uploadedCount / $requirementCount) * 100) : 0;
$programType = $pendaftaran->kategori_program === Pendaftaran::KATEGORI_PROGRAM_DEGREE
    ? 'Degree'
    : 'Non-degree';
?>

<div class="cal-dashboard registration-dashboard">
    <?= $this->render('_steps', ['activeStep' => $activeStep, 'readOnly' => $readOnly]) ?>
    <div class="registration-grid">
        <div class="registration-main">
            <div class="document-page-heading">
                <div>
                    <span class="eyebrow">ID PENDAFTARAN: <?= Html::encode((string) $pendaftaran->pendaftaran_id) ?> · TA 2026/2027</span>
                    <h1>Dokumen Kelayakan Akademik &amp; Imigrasi</h1>
                    <p class="muted">Unggah berkas yang sesuai dan pastikan informasi dapat dibaca dengan jelas.</p>
                </div>
                <div class="document-completion" aria-label="<?= $uploadedCount ?> dari <?= $requirementCount ?> dokumen diunggah">
                    <strong><?= $completionPercent ?>%</strong>
                    <span>PROGRES KELENGKAPAN<br><?= $uploadedCount ?> dari <?= $requirementCount ?> berkas diunggah</span>
                </div>
            </div>

            <div class="document-requirement-banner">
                <span aria-hidden="true">ⓘ</span>
                <strong>Matriks Dokumen Resmi KUI UNAND</strong>
                <small>Dokumen non-Inggris/Indonesia dapat memerlukan terjemahan tersumpah.</small>
                <span class="document-requirement-state <?= $uploadedCount === $requirementCount && $requirementCount > 0 ? 'is-complete' : '' ?>">
                    <?= $uploadedCount === $requirementCount && $requirementCount > 0 ? 'LENGKAP' : 'WAJIB LENGKAP' ?>
                </span>
            </div>

            <?= Html::beginForm(['dokumen'], 'post', ['enctype' => 'multipart/form-data', 'class' => 'candidate-form document-form']) ?>
            <?= $this->render('_readonly_notice', ['readOnly' => $readOnly]) ?>
            <section class="card-p document-list document-matrix">
                <div class="document-matrix-heading">
                    <h2>Berkas Pokok Program <?= Html::encode($programType) ?></h2>
                    <span><?= $requirementCount ?> berkas</span>
                </div>
                <?php foreach ($requirements as $index => $requirement): ?>
                    <?php $currentFile = $uploadedByRequirement[(int) $requirement->dokumen_id] ?? null; ?>
                    <article class="document-matrix-row">
                        <div class="document-type-icon" aria-hidden="true">
                            <?= match (true) {
                                stripos($requirement->nama_dokumen, 'paspor') !== false => '▤',
                                stripos($requirement->nama_dokumen, 'transkrip') !== false => '▧',
                                stripos($requirement->nama_dokumen, 'foto') !== false => '▣',
                                stripos($requirement->nama_dokumen, 'surat') !== false,
                                stripos($requirement->nama_dokumen, 'application') !== false,
                                stripos($requirement->nama_dokumen, 'recommendation') !== false => '▧',
                                default => '▤',
                            } ?>
                        </div>
                        <div class="document-copy">
                            <strong><?= Html::encode(($index + 1) . '. ' . $requirement->nama_dokumen) ?></strong>
                            <span class="document-required-tag <?= $requirement->wajib ? '' : 'is-optional' ?>">
                                <?= $requirement->wajib ? 'WAJIB' : 'OPSIONAL' ?>
                            </span>
                            <?php if ($currentFile): ?>
                                <?php $fileSize = $currentFile->file_path && is_file($currentFile->file_path) ? filesize($currentFile->file_path) : false; ?>
                                <small>
                                    <?= Html::a(
                                        Html::encode($currentFile->nama_file),
                                        ['unduh-dokumen', 'id' => $currentFile->dokumen_pendaftar_id],
                                    ) ?>
                                    <?php if ($fileSize !== false): ?>
                                        · <?= Yii::$app->formatter->asShortSize($fileSize) ?>
                                    <?php endif; ?>
                                </small>
                            <?php else: ?>
                                <small class="muted">Belum ada berkas yang diunggah.</small>
                            <?php endif; ?>
                        </div>
                        <div class="document-row-actions">
                            <?php if ($currentFile): ?>
                                <span class="document-upload-status is-uploaded"><span aria-hidden="true">✓</span> Terunggah</span>
                            <?php else: ?>
                                <span class="document-upload-status is-pending">Belum diunggah</span>
                            <?php endif; ?>
                            <?php if (!$readOnly): ?>
                                <label class="document-upload-button">
                                    <?= Html::fileInput('documents[' . $requirement->dokumen_id . ']', null, [
                                        'accept' => '.pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png',
                                        'aria-label' => ($currentFile ? 'Ganti ' : 'Unggah ') . $requirement->nama_dokumen,
                                    ]) ?>
                                    <span><?= $currentFile ? 'Ganti Berkas' : 'Unggah Berkas' ?></span>
                                </label>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
                <?php if (!$requirements): ?>
                    <p class="muted">Belum ada persyaratan dokumen aktif untuk kategori program ini.</p>
                <?php endif; ?>
                <p class="document-format-note">Format diterima: PDF, JPG, PNG · Maksimal 5 MB per berkas. Pilih berkas, lalu tekan Simpan Draf.</p>
            </section>
            <?php if (!$readOnly): ?>
                <div class="candidate-form-actions document-form-actions">
                    <?= Html::a('← Kembali: Tahap 3', ['akademik'], ['class' => 'btn-secondary']) ?>
                    <?= Html::submitButton('Simpan Draf', ['class' => 'btn-secondary']) ?>
                    <?= Html::a('Lanjut: Tahap 5 (Bantuan Visa) →', ['visa'], ['class' => 'btn-primary']) ?>
                </div>
            <?php else: ?>
                <div class="candidate-form-actions registration-actions">
                    <?= Html::a('← Kembali: Tahap 3 (Akademik)', ['akademik'], ['class' => 'btn-secondary']) ?>
                    <?= Html::a('Lanjut: Tahap 5 (Bantuan Visa) →', ['visa'], ['class' => 'btn-primary']) ?>
                </div>
            <?php endif; ?>
            <?= Html::endForm() ?>
        </div>
        <?= $this->render('_registration_aside', ['pendaftaran' => $pendaftaran, 'activeStep' => $activeStep]) ?>
    </div>
</div>

<?php
$this->registerJs(<<<'JS'
document.querySelectorAll('.document-upload-button input[type="file"]').forEach(function (input) {
            input.addEventListener('change', function () {
                if (input.files && input.files.length > 0) {
                    input.nextElementSibling.textContent = 'Dipilih: ' + input.files[0].name;
                }
            });
});
JS);
?>
