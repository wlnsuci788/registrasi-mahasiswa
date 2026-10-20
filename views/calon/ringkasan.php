<?php

use app\models\DokumenPendaftar;
use app\models\DokumenPersyaratan;
use app\models\Pendaftaran;
use app\models\PengajuanVisa;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var Pendaftaran $pendaftaran */
/** @var app\models\Fakultas|null $fakultas */
/** @var DokumenPersyaratan[] $requirements */
/** @var DokumenPendaftar[] $documents */
/** @var PengajuanVisa|null $visa */
/** @var string[] $submissionErrors */
/** @var int $activeStep */
/** @var bool $readOnly */

$this->title = 'Ringkasan & Kirim';
$visaData = $visa && $visa->data_pengajuan ? json_decode($visa->data_pengajuan, true) : [];
$documentsByRequirement = [];
foreach ($documents as $document) {
    $documentsByRequirement[(int) $document->dokumen_id] = $document;
}
$photoDocument = null;
foreach ($requirements as $requirement) {
    if (stripos($requirement->nama_dokumen, 'foto') !== false) {
        $photoDocument = $documentsByRequirement[(int) $requirement->dokumen_id] ?? null;
        break;
    }
}
$uploadedCount = 0;
foreach ($requirements as $requirement) {
    if (isset($documentsByRequirement[(int) $requirement->dokumen_id])) {
        $uploadedCount++;
    }
}
$requiredDocumentCount = count($requirements);
$isWni = $pendaftaran->jalur === Pendaftaran::JALUR_WNI;
$programName = $pendaftaran->kategori_program === Pendaftaran::KATEGORI_PROGRAM_DEGREE
    ? $pendaftaran->jenjang
    : $pendaftaran->jenis_program;
$programName = $programName ?: 'Belum dipilih';
$visaChoiceLabel = match ($pendaftaran->pilihan_visa) {
    Pendaftaran::PILIHAN_VISA_YA => 'Membutuhkan bantuan dari KUI',
    Pendaftaran::PILIHAN_VISA_TIDAK => 'Mengurus visa sendiri',
    default => 'Belum dipilih',
};
$canSubmit = !$readOnly && !$submissionErrors;
?>

<div class="cal-dashboard registration-dashboard review-dashboard">
    <?= $this->render('_steps', ['activeStep' => $activeStep, 'readOnly' => $readOnly]) ?>
    <?= $this->render('_readonly_notice', ['readOnly' => $readOnly]) ?>

    <div class="review-page-heading">
        <div>
            <span class="eyebrow">FORMULIR REGISTRASI MAHASISWA INTERNASIONAL</span>
            <h1>Penerimaan Mahasiswa Asing TA 2026/2027</h1>
            <p>Periksa kembali seluruh informasi sebelum mengirim pendaftaran.</p>
        </div>
        <span class="review-draft-status <?= $readOnly ? 'is-submitted' : '' ?>">
            <span aria-hidden="true"><?= $readOnly ? '✓' : '◷' ?></span>
            <?= Html::encode($pendaftaran->status ?: Pendaftaran::STATUS_DRAFT) ?>
            <?php if ($pendaftaran->updated_at): ?>
                · <?= Yii::$app->formatter->asTime($pendaftaran->updated_at, 'php:H:i') ?> WIB
            <?php endif; ?>
        </span>
    </div>

    <div class="review-layout">
        <main class="review-main">
            <?php if ($submissionErrors && !$readOnly): ?>
                <section class="card-p review-blocking-errors" role="alert">
                    <strong>Masih ada data yang perlu dilengkapi sebelum pendaftaran dapat dikirim:</strong>
                    <ul>
                        <?php foreach ($submissionErrors as $error): ?>
                            <li><?= Html::encode($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>

            <section class="card-p review-section review-step-card">
                <div class="review-card-heading">
                    <h2><span>1</span> Tahap 1: Data Diri</h2>
                    <?= Html::a('Ubah', ['form'], ['class' => 'review-edit']) ?>
                </div>
                <div class="review-grid">
                    <div><span>Nama Lengkap</span><strong><?= Html::encode($pendaftaran->nama_lengkap ?: 'Belum diisi') ?></strong></div>
                    <div><span>Kewarganegaraan</span><strong><?= Html::encode(($isWni ? 'WNI' : 'WNA') . ($pendaftaran->asal_negara ? ' (' . $pendaftaran->asal_negara . ')' : '')) ?></strong></div>
                    <div><span>Jenis Kelamin</span><strong><?= Html::encode($pendaftaran->jenis_kelamin ?: 'Belum diisi') ?></strong></div>
                    <div><span>Nomor Paspor / Identitas</span><strong><?= Html::encode($pendaftaran->no_paspor ?: 'Belum diisi') ?></strong></div>
                    <div><span>Tempat &amp; Tanggal Lahir</span><strong><?= Html::encode(trim(($pendaftaran->tempat_lahir ?: '') . ', ' . ($pendaftaran->tanggal_lahir ?: ''), ', ')) ?></strong></div>
                    <div><span>ID Pendaftaran</span><strong><?= Html::encode((string) $pendaftaran->pendaftaran_id) ?></strong></div>
                </div>
            </section>

            <section class="card-p review-section review-step-card">
                <div class="review-card-heading">
                    <h2><span>2</span> Tahap 2: Program</h2>
                    <?= Html::a('Ubah', ['program'], ['class' => 'review-edit']) ?>
                </div>
                <div class="review-grid review-program-grid">
                    <div><span>Kategori</span><strong><?= Html::encode(ucfirst($pendaftaran->kategori_program ?: 'Belum dipilih')) ?></strong></div>
                    <div><span>Jenjang / Program</span><strong><?= Html::encode($programName) ?></strong></div>
                    <div><span>Fakultas</span><strong><?= Html::encode($fakultas ? $fakultas->nama_fakultas : 'Belum dipilih') ?></strong></div>
                    <div><span>Pembiayaan</span><strong><?= Html::encode($pendaftaran->pembiayaan ?: 'Belum dipilih') ?></strong></div>
                    <?php if (!$pendaftaran->fakultas_id): ?>
                        <div><span>Institusi tujuan</span><strong>Universitas Andalas</strong></div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="card-p review-section review-step-card">
                <div class="review-card-heading">
                    <h2><span>3</span> Tahap 3: Akademik</h2>
                    <?= Html::a('Ubah', ['akademik'], ['class' => 'review-edit']) ?>
                </div>
                <div class="review-grid review-academic-grid">
                    <div><span>Asal Institusi</span><strong><?= Html::encode($pendaftaran->asal_institusi ?: 'Belum diisi') ?></strong></div>
                    <div><span>Nilai Akhir / IPK</span><strong><?= $pendaftaran->nilai_akhir !== null ? Html::encode((string) $pendaftaran->nilai_akhir) : 'Belum diisi' ?></strong></div>
                    <div><span>Negara / Kota Institusi</span><strong><?= Html::encode(trim(($pendaftaran->negara_institusi ?: '') . ' / ' . ($pendaftaran->kota_institusi ?: ''), ' /')) ?></strong></div>
                    <div><span>Pendidikan Terakhir</span><strong><?= Html::encode($pendaftaran->jenjang_pendidikan_terakhir ?: 'Belum diisi') ?></strong></div>
                    <div><span>Jurusan / Konsentrasi</span><strong><?= Html::encode($pendaftaran->jurusan_asal ?: 'Belum diisi') ?></strong></div>
                    <div><span>Skala Nilai / Tahun Lulus</span><strong><?= Html::encode(($pendaftaran->skala_nilai ?: 'Belum diisi') . ' / ' . ($pendaftaran->tahun_kelulusan ?: 'Belum diisi')) ?></strong></div>
                    <div><span>Tes Bahasa Inggris</span><strong><?= Html::encode($pendaftaran->jenis_tes_bahasa_inggris ?: 'Belum diisi') ?><?= $pendaftaran->skor_tes_bahasa_inggris !== null ? ' · ' . Html::encode((string) $pendaftaran->skor_tes_bahasa_inggris) : '' ?></strong></div>
                    <div><span>Bahasa Indonesia</span><strong><?= Html::encode($pendaftaran->tingkat_bahasa_indonesia ?: 'Belum diisi') ?></strong></div>
                    <div><span>Transfer Kredit</span><strong><?= $pendaftaran->transfer_kredit ? 'Ya' : 'Tidak' ?><?= $pendaftaran->target_kredit ? ' · ' . Html::encode((string) $pendaftaran->target_kredit) . ' SKS/ECTS' : '' ?></strong></div>
                    <div><span>Pembiayaan</span><strong><?= Html::encode($pendaftaran->pembiayaan ?: 'Belum dipilih') ?></strong></div>
                    <div><span>Lama Kunjungan</span><strong><?= Html::encode($pendaftaran->lama_kunjungan ?: ($pendaftaran->kategori_program === Pendaftaran::KATEGORI_PROGRAM_DEGREE ? 'Tidak berlaku' : 'Belum diisi')) ?></strong></div>
                </div>
            </section>

            <section class="card-p review-section review-step-card">
                <div class="review-card-heading">
                    <h2>
                        <span>4</span> Tahap 4: Dokumen
                        <small>(<?= $uploadedCount ?>/<?= $requiredDocumentCount ?> lengkap)</small>
                    </h2>
                    <?= Html::a('Ubah', ['dokumen'], ['class' => 'review-edit']) ?>
                </div>
                <?php if ($requirements): ?>
                    <ul class="review-document-checklist">
                        <?php foreach ($requirements as $requirement): ?>
                            <?php $document = $documentsByRequirement[(int) $requirement->dokumen_id] ?? null; ?>
                            <li class="<?= $document ? 'is-complete' : 'is-missing' ?>">
                                <span class="review-document-check" aria-hidden="true"><?= $document ? '✓' : '!' ?></span>
                                <span><?= Html::encode($requirement->nama_dokumen) ?></span>
                                <?php if ($document): ?>
                                    <?= Html::a(
                                        'Lihat',
                                        ['unduh-dokumen', 'id' => $document->dokumen_pendaftar_id],
                                        ['class' => 'review-document-download', 'aria-label' => 'Unduh ' . $requirement->nama_dokumen],
                                    ) ?>
                                <?php else: ?>
                                    <small><?= $requirement->wajib ? 'Wajib' : 'Opsional' ?></small>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="muted">Belum ada persyaratan dokumen yang aktif.</p>
                <?php endif; ?>
            </section>

            <section class="card-p review-section review-step-card">
                <div class="review-card-heading">
                    <h2><span>5</span> Tahap 5: Bantuan Visa</h2>
                    <?= Html::a('Ubah', ['visa'], ['class' => 'review-edit']) ?>
                </div>
                <?php if ($isWni): ?>
                    <div class="review-visa-summary"><span>Bantuan Visa</span><strong>Tidak diperlukan untuk pendaftar WNI</strong></div>
                <?php else: ?>
                    <div class="review-grid review-visa-grid">
                        <div><span>Bantuan Visa</span><strong><?= Html::encode($visaChoiceLabel) ?></strong></div>
                        <?php if ($pendaftaran->pilihan_visa === Pendaftaran::PILIHAN_VISA_YA): ?>
                            <div><span>Jenis Visa</span><strong><?= Html::encode($pendaftaran->jenis_visa ?: 'Belum diisi') ?></strong></div>
                            <div><span>Masa Berlaku Paspor</span><strong><?= Html::encode($visa ? $visa->masa_berlaku_paspor : 'Belum diisi') ?></strong></div>
                            <?php foreach (
                            [
                                'tujuan' => 'Tujuan',
                                'tanggal_kedatangan' => 'Perkiraan Kedatangan',
                                'kota_kedatangan' => 'Kota / Bandara',
                                'alamat_selama_di_padang' => 'Alamat di Padang',
                            ] as $key => $label
): ?>
                                <div><span><?= Html::encode($label) ?></span><strong><?= Html::encode(is_array($visaData) ? ($visaData[$key] ?? 'Belum diisi') : 'Belum diisi') ?></strong></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>

            <?php if (!$readOnly): ?>
                <section class="card-p review-integrity">
                    <h2>Pernyataan Integritas dan Keabsahan Data</h2>
                    <p>Dengan ini saya menyatakan bahwa seluruh data dan dokumen yang saya unggah adalah asli, sah, dan dapat dipertanggungjawabkan secara hukum.</p>
                    <label for="review-integrity">
                        <?= Html::checkbox('integrity', false, [
                            'id' => 'review-integrity',
                            'value' => '1',
                            'form' => 'registration-submit-form',
                            'required' => true,
                        ]) ?>
                        Saya menyetujui ketentuan seleksi mahasiswa Universitas Andalas dan siap menerima sanksi pembatalan status apabila ditemukan ketidaksesuaian data atau dokumen.
                    </label>
                    <div class="review-submission-note">
                        <span aria-hidden="true">ⓘ</span>
                        Setelah dikirim, status pendaftaran menjadi <strong>Terkirim</strong> dan diverifikasi oleh International Office UNAND.
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($readOnly): ?>
                <section class="card-p registration-readonly-confirm">
                    <strong>Pendaftaran sudah dikirim</strong>
                    <p class="muted">Halaman ini hanya untuk pemeriksaan. Anda dapat membuka tahap lain dari navigasi tahapan di atas.</p>
                    <?= Html::a('Lihat Status Pendaftaran', ['status'], ['class' => 'btn-primary']) ?>
                </section>
            <?php endif; ?>

            <div class="candidate-form-actions review-back-action">
                <?= Html::a('← Kembali: Tahap 5', ['visa'], ['class' => 'btn-secondary']) ?>
            </div>
        </main>

        <aside class="review-aside">
            <section class="card-p review-applicant-card">
                <div class="review-applicant-heading">
                    <?php if ($photoDocument): ?>
                        <?= Html::img(Url::to(['foto-dokumen', 'id' => $photoDocument->dokumen_pendaftar_id]), [
                            'class' => 'review-applicant-photo',
                            'alt' => 'Pas foto ' . ($pendaftaran->nama_lengkap ?: 'calon mahasiswa'),
                        ]) ?>
                    <?php else: ?>
                        <?= Html::img(Url::to('@web/images/avatar-mahasiswa.svg'), [
                            'class' => 'review-applicant-photo',
                            'alt' => 'Ilustrasi foto contoh mahasiswa laki-laki',
                        ]) ?>
                    <?php endif; ?>
                    <div>
                        <strong><?= Html::encode($pendaftaran->nama_lengkap ?: 'Calon Mahasiswa') ?></strong>
                        <small>ID Pendaftaran: <?= Html::encode((string) $pendaftaran->pendaftaran_id) ?></small>
                        <?php if (!$photoDocument): ?>
                            <small class="review-photo-placeholder-note">Foto contoh — unggah pas foto Anda</small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="review-readiness">
                    <div><span>Dokumen</span><strong><?= $requiredDocumentCount > 0 ? (int) round(($uploadedCount / $requiredDocumentCount) * 100) : 0 ?>% Lengkap</strong></div>
                    <div><span>Kelengkapan Data</span><strong class="<?= $submissionErrors ? 'is-pending' : 'is-ready' ?>"><?= $submissionErrors ? 'Perlu Dilengkapi' : 'Siap Dikirim' ?></strong></div>
                </div>
                <dl>
                    <dt>Kewarganegaraan</dt>
                    <dd><?= Html::encode(($isWni ? 'WNI' : 'WNA') . ($pendaftaran->asal_negara ? ' (' . $pendaftaran->asal_negara . ')' : '')) ?></dd>
                    <dt>Jenjang &amp; Fakultas</dt>
                    <dd><?= Html::encode(trim(($programName !== 'Belum dipilih' ? $programName : '') . ($fakultas ? ' · ' . $fakultas->nama_fakultas : ''), ' ·') ?: 'Belum dipilih') ?></dd>
                    <dt>Pembiayaan</dt>
                    <dd><?= Html::encode($pendaftaran->pembiayaan ?: 'Belum dipilih') ?></dd>
                    <dt>Tahun Akademik</dt>
                    <dd>2026/2027</dd>
                </dl>
            </section>

            <?php if (!$readOnly): ?>
                <section class="card-p review-send-card">
                    <div class="review-send-icon" aria-hidden="true"><?= $canSubmit ? '➤' : '!' ?></div>
                    <h2><?= $canSubmit ? 'Siap Kirim Pendaftaran' : 'Periksa Kelengkapan Data' ?></h2>
                    <p><?= $canSubmit
                        ? 'Setelah dikirim, status pendaftaran menjadi Terkirim dan diverifikasi oleh International Office UNAND.'
                        : 'Lengkapi data yang ditandai sebelum mengirim pendaftaran.' ?></p>
                    <?= Html::beginForm(['ringkasan'], 'post', ['id' => 'registration-submit-form']) ?>
                    <?= Html::submitButton('Kirim Pendaftaran', [
                        'class' => 'btn-primary review-send-button',
                        'disabled' => !$canSubmit,
                    ]) ?>
                    <?= Html::endForm() ?>
                    <?= Html::a('← Kembali: Tahap 5', ['visa'], ['class' => 'review-send-back']) ?>
                </section>
            <?php endif; ?>

        </aside>
    </div>
</div>
