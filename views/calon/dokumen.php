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
?>

<div class="cal-dashboard registration-dashboard">
    <?= $this->render('_steps', ['activeStep' => $activeStep, 'readOnly' => $readOnly]) ?>
    <div class="registration-grid">
        <div class="registration-main">
    <div class="cal-greeting">
        <div>
            <div class="eyebrow">TAHAP 4 DARI 6</div>
            <h1>Unggah Dokumen</h1>
            <p class="muted">Berkas PDF, JPG, atau PNG; ukuran maksimal 5 MB per berkas.</p>
        </div>
    </div>

    <?= Html::beginForm(['dokumen'], 'post', ['enctype' => 'multipart/form-data', 'class' => 'candidate-form']) ?>
    <?= $this->render('_readonly_notice', ['readOnly' => $readOnly]) ?>
    <section class="card-p document-list">
        <?php foreach ($requirements as $requirement): ?>
            <?php $currentFile = $uploadedByRequirement[(int) $requirement->dokumen_id] ?? null; ?>
            <div class="document-row">
                <div class="document-copy">
                    <strong><?= Html::encode($requirement->nama_dokumen) ?></strong>
                    <span><?= $requirement->wajib ? 'Wajib' : 'Opsional' ?></span>
                    <?php if ($currentFile): ?>
                        <small>
                            Berkas tersimpan:
                            <?= Html::a(Html::encode($currentFile->nama_file), ['unduh-dokumen', 'id' => $currentFile->dokumen_pendaftar_id]) ?>
                        </small>
                    <?php else: ?>
                        <small class="muted">Belum ada berkas yang diunggah.</small>
                    <?php endif; ?>
                </div>
                <?php if (!$readOnly): ?>
                    <label class="file-input-label">
                        <?= Html::fileInput('documents[' . $requirement->dokumen_id . ']', null, [
                            'accept' => '.pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png',
                        ]) ?>
                        <span><?= $currentFile ? 'Ganti Berkas' : 'Pilih Berkas' ?></span>
                    </label>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <?php if (!$requirements): ?>
            <p class="muted">Belum ada persyaratan dokumen aktif untuk kategori program ini.</p>
        <?php endif; ?>
    </section>
    <?php if (!$readOnly): ?>
        <div class="candidate-form-actions">
        <?= Html::a('← Kembali ke Akademik', ['akademik'], ['class' => 'btn-secondary']) ?>
        <?= Html::submitButton('Simpan Berkas', ['class' => 'btn-secondary']) ?>
        <?= Html::a('Lanjut ke Bantuan Visa →', ['visa'], ['class' => 'btn-primary']) ?>
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
