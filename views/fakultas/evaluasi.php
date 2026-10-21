<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $applicant */
/** @var string|null $error */
/** @var string $evaluatorName */

$this->title = 'Evaluasi Akademik Fakultas Kedokteran';
$evaluated = $applicant['saran_fakultas'] !== '';
$previewUrl = Url::to(['/fakultas/dokumen-pdf', 'id' => $applicant['pendaftaran_id']]);
$previewFrameUrl = $previewUrl . '#toolbar=1&navpanes=0&view=FitH';
$downloadUrl = Url::to(['/fakultas/dokumen-pdf', 'id' => $applicant['pendaftaran_id'], 'download' => 1]);
$birthDate = new DateTimeImmutable($applicant['tanggal_lahir']);
$monthNames = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember',
];
$formattedBirthDate = $birthDate->format('j') . ' ' . $monthNames[(int) $birthDate->format('n')] . ' ' . $birthDate->format('Y');
?>
<div class="faculty-breadcrumb"><?= Html::a('Dashboard FK', ['/fakultas/index']) ?> <span>›</span> Fakultas <span>›</span> <?= Html::encode($applicant['nama_lengkap']) ?> (ID: <?= Html::encode(str_pad((string) $applicant['pendaftaran_id'], 4, '0', STR_PAD_LEFT)) ?>)</div>
<section class="faculty-page-heading">
    <div><h1>Fakultas – Evaluasi Akademik Kedokteran <span class="faculty-title-chip">● Fakultas</span></h1></div>
    <div class="faculty-page-tools"><span>▣ Batas Sidang<br><strong>T.A. 2026/2027</strong></span><span>◷ Log</span></div>
</section>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="faculty-alert"><?= Html::encode(Yii::$app->session->getFlash('success')) ?></div>
<?php endif; ?>

<div class="faculty-evaluation-grid">
    <main>
        <section class="faculty-panel faculty-candidate-card">
            <div class="faculty-candidate-portrait">
                <?= Html::img(Url::to('@web/images/avatar-mahasiswa.svg'), ['alt' => 'Foto dummy ' . $applicant['nama_lengkap']]) ?>
                <span class="faculty-online-indicator" aria-label="Status aktif"></span>
                <small><?= Html::encode($applicant['asal_negara']) ?></small>
            </div>
            <div class="faculty-candidate-info">
                <h2><?= Html::encode($applicant['nama_lengkap']) ?> <span>ID: <?= Html::encode(str_pad((string) $applicant['pendaftaran_id'], 4, '0', STR_PAD_LEFT)) ?></span></h2>
                <p>Fakultas: <strong>Fakultas Kedokteran</strong> · Tempat &amp; Tgl Lahir: <strong><?= Html::encode($applicant['tempat_lahir'] . ', ' . $formattedBirthDate) ?></strong></p>
                <div class="faculty-candidate-meta"><span>No. Paspor<strong>▣ <?= Html::encode($applicant['no_paspor']) ?></strong></span><span>Negara Asal<strong>🌐 <?= Html::encode($applicant['asal_negara']) ?></strong></span><span>Tahun Akademik<strong>▦ 2026/2027</strong></span></div>
            </div>
            <span class="faculty-program-chip">♧ <?= Html::encode($applicant['program_studi']) ?></span>
        </section>

        <section class="faculty-panel faculty-transcript">
            <div class="faculty-transcript-heading"><div><h2>▣ Penelaahan Berkas Akademik</h2></div><span class="faculty-verified">Terverifikasi</span></div>
            <div class="faculty-document-actions">
                <strong>▤ Transkrip Akademik Tersumpah (Legalized)</strong>
                <?= Html::button('↗ Lihat', [
                    'id' => 'faculty-document-toggle',
                    'type' => 'button',
                    'aria-expanded' => 'false',
                    'aria-controls' => 'faculty-document-preview',
                    'title' => 'Tampilkan dokumen PDF',
                ]) ?>
                <?= Html::a('⇩ Unduh PDF', $downloadUrl, [
                    'title' => 'Unduh transkrip akademik dummy dalam format PDF',
                ]) ?>
            </div>
            <div class="faculty-file-banner"><span class="faculty-file-icon">PDF</span><div><strong><?= Html::encode($applicant['nama_berkas']) ?></strong><small>Validasi Kedutaan · ID: <?= Html::encode(str_pad((string) $applicant['pendaftaran_id'], 4, '0', STR_PAD_LEFT)) ?> · Dokumen Sah</small></div><span class="faculty-file-status">✓ Memenuhi Syarat</span></div>
            <div id="faculty-document-preview" hidden>
                <div class="faculty-preview-caption">◎ DOKUMEN AKADEMIK TERSUMPAH <span>Dokumen PDF</span></div>
                <div class="faculty-pdf-preview">
                    <iframe
                        data-src="<?= Html::encode($previewFrameUrl) ?>"
                        title="Lihat transkrip PDF dummy <?= Html::encode($applicant['nama_lengkap']) ?>"
                        loading="lazy"></iframe>
                </div>
            </div>
        </section>
    </main>

    <aside class="faculty-panel faculty-recommendation">
        <div class="faculty-recommendation-heading"><span>FORMULIR EVALUASI <b>Fakultas</b></span><h2>Rekomendasi Fakultas Kedokteran</h2></div>
        <?php if ($error):
            ?><div class="faculty-alert faculty-alert-error"><?= Html::encode($error) ?></div><?php
        endif; ?>
        <?php if ($evaluated): ?>
            <div class="faculty-saved-note"><strong>Rekomendasi tersimpan: <?= Html::encode($applicant['saran_fakultas']) ?></strong><p><?= Html::encode($applicant['catatan_fakultas']) ?></p></div>
        <?php endif; ?>
        <form method="post">
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
            <fieldset class="faculty-radio-group">
                <legend>REKOMENDASI SELEKSI AKADEMIK</legend>
                <label class="faculty-radio-option faculty-radio-yes">
                    <input type="radio" name="saran_fakultas" value="Disarankan lolos" <?= $applicant['saran_fakultas'] === 'Disarankan lolos' ? 'checked' : '' ?> required>
                    <span><strong>✓ Disarankan lolos</strong><small>Kandidat memenuhi kualifikasi dan standar akademik S1 Pendidikan Dokter.</small></span>
                </label>
                <label class="faculty-radio-option faculty-radio-no">
                    <input type="radio" name="saran_fakultas" value="Disarankan tidak lolos" <?= $applicant['saran_fakultas'] === 'Disarankan tidak lolos' ? 'checked' : '' ?> required>
                    <span><strong>⊗ Disarankan tidak lolos</strong><small>Kandidat belum memenuhi kualifikasi atau standar akademik yang ditentukan.</small></span>
                </label>
            </fieldset>
            <label class="faculty-note-label" for="faculty-note">Catatan Verifikator Fakultas <span>Maks. 500 karakter</span></label>
            <textarea id="faculty-note" class="faculty-note-input" name="catatan_fakultas" rows="5" maxlength="500" placeholder="Tuliskan catatan akademik..." ><?= Html::encode($applicant['catatan_fakultas']) ?></textarea>
            <div class="faculty-evaluator-chip"><span>HS</span><div><strong><?= Html::encode($evaluatorName) ?></strong><small>Verifikator Fakultas Kedokteran</small></div></div>
            <button class="faculty-save-button" type="submit">✓ <?= $evaluated ? 'Perbarui Rekomendasi' : 'Simpan Rekomendasi' ?></button>
        </form>
        <div class="faculty-final-note">ⓘ Keputusan akhir ditetapkan oleh International Office</div>
    </aside>
</div>
<?php
$this->registerJs(<<<'JS'
const documentToggle = document.getElementById('faculty-document-toggle');
const documentPreview = document.getElementById('faculty-document-preview');
const documentFrame = documentPreview.querySelector('iframe');

documentToggle.addEventListener('click', () => {
    const isVisible = documentToggle.getAttribute('aria-expanded') === 'true';
    documentToggle.setAttribute('aria-expanded', String(!isVisible));
    documentToggle.textContent = isVisible ? '↗ Lihat' : 'Tutup';
    documentToggle.title = isVisible ? 'Tampilkan dokumen PDF' : 'Sembunyikan dokumen PDF';
    documentPreview.hidden = isVisible;

    if (!isVisible && !documentFrame.hasAttribute('src')) {
        documentFrame.src = documentFrame.dataset.src;
    }
});
JS
);
?>
