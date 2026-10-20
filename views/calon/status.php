<?php

use app\models\DokumenPendaftar;
use app\models\DokumenPersyaratan;
use app\models\Pendaftaran;
use app\models\PengajuanVisa;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var Pendaftaran|null $pendaftaran */
/** @var int $completedSteps */
/** @var DokumenPersyaratan[] $requirements */
/** @var DokumenPendaftar[] $documents */
/** @var PengajuanVisa|null $visa */

$this->title = 'Status Pendaftaran';
$statusClass = match ($pendaftaran?->status) {
    Pendaftaran::STATUS_LOLOS, Pendaftaran::STATUS_REGISTRATION_NUMBER_TERBIT => 'is-success',
    Pendaftaran::STATUS_TERKIRIM => 'is-progress',
    Pendaftaran::STATUS_TIDAK_LOLOS => 'is-danger',
    default => 'is-muted',
};
$isSubmitted = $pendaftaran && $pendaftaran->status !== Pendaftaran::STATUS_DRAFT;
$isDecisionFinal = $pendaftaran && in_array($pendaftaran->status, [
    Pendaftaran::STATUS_LOLOS,
    Pendaftaran::STATUS_TIDAK_LOLOS,
    Pendaftaran::STATUS_REGISTRATION_NUMBER_TERBIT,
], true);
$completedStageCount = $isDecisionFinal ? 6 : ($isSubmitted ? 5 : $completedSteps);
$uploadedByRequirement = [];
foreach ($documents as $document) {
    $uploadedByRequirement[(int) $document->dokumen_id] = $document;
}
$uploadedCount = 0;
foreach ($requirements as $requirement) {
    if (isset($uploadedByRequirement[(int) $requirement->dokumen_id])) {
        $uploadedCount++;
    }
}
$requiredCount = count($requirements);
$visaStatus = !$pendaftaran || $pendaftaran->jalur === Pendaftaran::JALUR_WNI
    ? 'Tidak diperlukan'
    : match ($pendaftaran->pilihan_visa) {
        Pendaftaran::PILIHAN_VISA_TIDAK => 'Mengurus sendiri',
        Pendaftaran::PILIHAN_VISA_YA => $visa?->status ?: 'Menunggu pengajuan',
        default => 'Belum diisi',
    };
$programName = $pendaftaran
    ? trim(implode(' ', array_filter([$pendaftaran->jenjang, $pendaftaran->jenis_program ?: $pendaftaran->kategori_program])))
    : '';
$steps = [
    ['Data Diri', 'Identitas pendaftar', 'form'],
    ['Program', $programName ?: 'Pilihan program studi', 'program'],
    ['Akademik', $pendaftaran?->asal_institusi ?: 'Riwayat nilai & pendidikan', 'akademik'],
    ['Dokumen', $uploadedCount . ' dari ' . $requiredCount . ' berkas diunggah', 'dokumen'],
    ['Bantuan Visa', $visaStatus, 'visa'],
    ['Ringkasan & Kirim', $isDecisionFinal ? $pendaftaran->status : ($isSubmitted ? 'Menunggu pemeriksaan petugas' : 'Finalisasi & registrasi akhir'), 'ringkasan'],
];
$stageLabel = $isDecisionFinal
    ? $pendaftaran->status
    : ($isSubmitted ? 'Menunggu verifikasi' : ($completedSteps ? 'Draft tersimpan' : 'Belum dimulai'));
$photoDocument = null;
foreach ($requirements as $requirement) {
    if (stripos($requirement->nama_dokumen, 'foto') !== false) {
        $photoDocument = $uploadedByRequirement[(int) $requirement->dokumen_id] ?? null;
        break;
    }
}
?>

<div class="cal-dashboard applicant-dashboard status-dashboard">
    <section class="applicant-welcome status-welcome">
        <div>
            <span class="eyebrow">PORTAL PENERIMAAN ASING</span>
            <h1>Status Pendaftaran</h1>
            <p>Pantau tahapan seleksi dan perkembangan pendaftaran Anda.</p>
        </div>
        <span class="academic-year">● Aktif 2026/2027</span>
    </section>

    <?php if (!$pendaftaran): ?>
        <section class="card-p applicant-empty-state">
            <span class="applicant-summary-icon" aria-hidden="true">▣</span>
            <div>
                <h2>Belum ada pendaftaran</h2>
                <p>Mulai isi formulir untuk melihat status pendaftaran Anda di halaman ini.</p>
                <?= Html::a('Buka Formulir Registrasi', ['form'], ['class' => 'btn-primary']) ?>
            </div>
        </section>
    <?php else: ?>
        <section class="card-p status-applicant-card">
            <div class="status-applicant-main">
                <?php if ($photoDocument): ?>
                    <?= Html::img(Url::to(['foto-dokumen', 'id' => $photoDocument->dokumen_pendaftar_id]), [
                        'class' => 'status-applicant-photo',
                        'alt' => 'Pas foto ' . ($pendaftaran->nama_lengkap ?: 'calon mahasiswa'),
                    ]) ?>
                <?php else: ?>
                    <span class="status-applicant-initial" aria-hidden="true"><?= Html::encode(mb_strtoupper(mb_substr($pendaftaran->nama_lengkap ?: 'P', 0, 1))) ?></span>
                <?php endif; ?>
                <div>
                    <h2><?= Html::encode($pendaftaran->nama_lengkap ?: 'Calon Mahasiswa') ?> <small>(ID: <?= Html::encode((string) $pendaftaran->pendaftaran_id) ?>)</small></h2>
                    <strong><?= Html::encode(trim($programName . ' ' . ($pendaftaran->fakultas?->nama_fakultas ?? ''))) ?></strong>
                    <span><?= Html::encode($pendaftaran->fakultas?->nama_fakultas ?: 'Fakultas belum dipilih') ?></span>
                </div>
                <span class="status-pill <?= $statusClass ?>"><?= Html::encode($pendaftaran->status ?: 'Draft') ?></span>
            </div>
            <div class="status-applicant-meta">
                <div><span>Nomor Pendaftaran</span><strong><?= Html::encode($pendaftaran->registration_number ?: 'Belum diterbitkan') ?></strong></div>
                <div><span>Bantuan Visa</span><strong><?= Html::encode($visaStatus) ?></strong></div>
                <div><span>Tahun Akademik</span><strong>2026/2027</strong></div>
            </div>
        </section>

        <section class="card-p status-note-card">
            <div class="status-note-icon" aria-hidden="true">✓</div>
            <div>
                <div class="status-note-heading">
                    <strong>Catatan Pendaftaran</strong>
                    <span><?= $pendaftaran->keputusan_at ? Html::encode(Yii::$app->formatter->asDate($pendaftaran->keputusan_at)) : 'Informasi terbaru' ?></span>
                </div>
                <p><?= Html::encode($pendaftaran->catatan_keputusan ?: (
                    $isSubmitted
                        ? 'Pendaftaran Anda telah diterima oleh sistem. Petugas International Office akan meninjau berkas dan informasi yang dikirim.'
                        : 'Pendaftaran masih berupa draft. Lengkapi seluruh tahap formulir sebelum mengirim untuk proses verifikasi.'
                )) ?></p>
            </div>
        </section>

        <?php if (!$isSubmitted): ?>
            <div class="status-locked-notice">
                <span aria-hidden="true">♙</span>
                <span>Lanjutkan Pengisian Formulir <small>Lengkapi tahap yang masih tertunda sebelum pengiriman.</small></span>
                <?= Html::a('Lanjutkan', ['form'], ['class' => 'btn-primary']) ?>
            </div>
        <?php endif; ?>

        <section class="card-p status-timeline-card">
            <div class="applicant-section-heading">
                <div><span class="eyebrow">PROGRES FORMULIR DAN PROSES SELEKSI</span><h2>Tahapan Pendaftaran</h2></div>
                <span class="progress-count">Tahap <?= min($completedStageCount + ($isDecisionFinal ? 0 : 1), 6) ?> dari 6</span>
            </div>
            <ol class="status-timeline">
                <?php foreach ($steps as $index => [$title, $detail, $route]): ?>
                    <?php
                    $done = $index < $completedStageCount;
                    $active = !$done && $index === $completedStageCount;
                    $stepState = $done ? 'Selesai' : ($active ? $stageLabel : 'Menunggu');
                    $stepUrl = $isSubmitted ? ['status'] : [$route];
                    ?>
                    <li class="<?= $done ? 'is-done' : '' ?> <?= $active ? 'is-active' : '' ?>">
                        <a href="<?= Html::encode(Url::to($stepUrl)) ?>">
                            <span class="status-timeline-marker"><?= $done ? '✓' : ($active ? '◷' : (string) ($index + 1)) ?></span>
                            <span class="status-timeline-copy">
                                <strong><?= Html::encode($title) ?></strong>
                                <small><?= Html::encode($detail) ?></small>
                            </span>
                            <span class="status-timeline-state"><?= Html::encode($stepState) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ol>
        </section>

        <section class="card-p status-contact-card">
            <div class="status-contact-icon" aria-hidden="true">☎</div>
            <div>
                <strong>Layanan Kantor Urusan Internasional (KUI)</strong>
                <span>Universitas Andalas · Gedung Rektorat Lt. 2, Limau Manis, Padang</span>
                <small>Senin–Jumat, 08.00–16.00 WIB · io@unand.ac.id</small>
            </div>
            <?= Html::a('Bantuan', ['bantuan'], ['class' => 'applicant-text-link']) ?>
        </section>
    <?php endif; ?>
</div>
