<?php

use app\models\DokumenPendaftar;
use app\models\DokumenPersyaratan;
use app\models\Pendaftaran;
use app\models\PengajuanVisa;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var Pendaftaran|null $pendaftaran */
/** @var int $completedSteps */
/** @var DokumenPersyaratan[] $requirements */
/** @var DokumenPendaftar[] $documents */
/** @var PengajuanVisa|null $visa */

$this->title = 'Beranda Pendaftar';
$user = Yii::$app->user->identity;
if (!$user instanceof User) {
    throw new \UnexpectedValueException('The authenticated identity is not a user account.');
}

$name = $pendaftaran && $pendaftaran->nama_lengkap ? $pendaftaran->nama_lengkap : $user->nama;
$status = $pendaftaran ? ($pendaftaran->status ?: 'Status belum ditentukan') : 'Belum mendaftar';
$statusClass = match ($status) {
    Pendaftaran::STATUS_LOLOS, Pendaftaran::STATUS_REGISTRATION_NUMBER_TERBIT => 'is-success',
    Pendaftaran::STATUS_TERKIRIM => 'is-progress',
    Pendaftaran::STATUS_TIDAK_LOLOS => 'is-danger',
    default => 'is-muted',
};
$isSubmitted = $pendaftaran && $pendaftaran->status !== Pendaftaran::STATUS_DRAFT;
$completedStageCount = $isSubmitted
    ? (in_array($pendaftaran->status, [Pendaftaran::STATUS_LOLOS, Pendaftaran::STATUS_TIDAK_LOLOS, Pendaftaran::STATUS_REGISTRATION_NUMBER_TERBIT], true) ? 6 : 5)
    : $completedSteps;
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
$documentsComplete = $requiredCount > 0 && $uploadedCount >= $requiredCount;
$photoDocument = null;
foreach ($requirements as $requirement) {
    if (stripos($requirement->nama_dokumen, 'foto') !== false) {
        $photoDocument = $uploadedByRequirement[(int) $requirement->dokumen_id] ?? null;
        break;
    }
}
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
    ['Data Diri', 'form'],
    ['Program', 'program'],
    ['Akademik', 'akademik'],
    ['Dokumen', 'dokumen'],
    ['Bantuan Visa', 'visa'],
    ['Ringkasan & Kirim', 'ringkasan'],
];
?>

<div class="cal-dashboard applicant-dashboard">
    <section class="applicant-welcome">
        <div>
            <span class="eyebrow">PORTAL PENERIMAAN MAHASISWA ASING</span>
            <h1>Selamat datang, <?= Html::encode($name) ?></h1>
            <p>Pantau proses pendaftaran dan lengkapi tahap yang masih tertunda.</p>
        </div>
        <span class="academic-year">TA 2026/2027</span>
    </section>

    <?php if (!$pendaftaran): ?>
        <section class="card-p applicant-empty-state">
            <span class="applicant-summary-icon" aria-hidden="true">▣</span>
            <div>
                <h2>Mulai pendaftaran Anda</h2>
                <p>Isi formulir registrasi untuk melihat informasi program dan status pendaftaran di halaman ini.</p>
                <?= Html::a('Mulai Formulir Registrasi', ['form'], ['class' => 'btn-primary']) ?>
            </div>
        </section>
    <?php else: ?>
        <section class="card-p applicant-identity-card">
            <div class="applicant-profile">
                <?php if ($photoDocument): ?>
                    <?= Html::img(Url::to(['foto-dokumen', 'id' => $photoDocument->dokumen_pendaftar_id]), [
                        'class' => 'applicant-profile-photo',
                        'alt' => 'Pas foto ' . $name,
                    ]) ?>
                <?php else: ?>
                    <?= Html::img(Url::to('@web/images/avatar-mahasiswa.svg'), [
                        'class' => 'applicant-profile-photo',
                        'alt' => 'Ilustrasi foto contoh mahasiswa',
                    ]) ?>
                <?php endif; ?>
                <div class="applicant-profile-copy">
                    <div class="applicant-badges">
                        <?php if ($pendaftaran->asal_negara): ?>
                            <span class="applicant-badge"><?= Html::encode($pendaftaran->asal_negara) ?></span>
                        <?php endif; ?>
                        <span class="status-pill <?= $statusClass ?>"><?= Html::encode($status) ?></span>
                    </div>
                    <h2><?= Html::encode($name) ?></h2>
                    <p>
                        <?= Html::encode($pendaftaran->jalur === Pendaftaran::JALUR_WNI ? 'WNI' : 'Pendaftar internasional') ?>
                        <?php if ($pendaftaran->no_paspor):
                            ?> · Paspor: <?= Html::encode($pendaftaran->no_paspor) ?><?php
                        endif; ?>
                    </p>
                    <?php if (!$photoDocument): ?>
                        <small>Foto contoh — unggah pas foto Anda pada tahap Dokumen.</small>
                    <?php endif; ?>
                </div>
            </div>
            <div class="applicant-registration-number">
                <span>NOMOR PENDAFTARAN</span>
                <strong><?= Html::encode($pendaftaran->registration_number ?: 'Belum diterbitkan') ?></strong>
            </div>
            <div class="applicant-program-card">
                <div>
                    <span class="eyebrow">PILIHAN PROGRAM STUDI</span>
                    <strong><?= Html::encode($programName ?: 'Belum dipilih') ?></strong>
                    <small><?= Html::encode($pendaftaran->fakultas?->nama_fakultas ?: 'Fakultas belum dipilih') ?></small>
                </div>
                <span class="program-type-badge"><?= Html::encode($pendaftaran->kategori_program === Pendaftaran::KATEGORI_PROGRAM_NON_DEGREE ? 'Program Jangka Pendek' : 'Program Bergelar') ?></span>
            </div>
        </section>

        <section class="applicant-progress card-p">
            <div class="applicant-section-heading">
                <div>
                    <span class="eyebrow">FORMULIR REGISTRASI MAHASISWA INTERNASIONAL</span>
                    <h2>Tahapan Pendaftaran</h2>
                </div>
                <span class="progress-count">Tahap <?= min($completedStageCount + 1, 6) ?> dari 6</span>
            </div>
            <div class="applicant-step-track">
                <?php foreach ($steps as $index => [$label, $route]): ?>
                    <?php
                    $done = $index < $completedStageCount;
                    $active = !$done && $index === $completedStageCount;
                    $stepUrl = $isSubmitted ? ['status'] : [$route];
                    ?>
                    <a class="applicant-step <?= $done ? 'is-done' : '' ?> <?= $active ? 'is-active' : '' ?>"
                       href="<?= Html::encode(Url::to($stepUrl)) ?>">
                        <span class="applicant-step-dot"><?= $done ? '✓' : (string) ($index + 1) ?></span>
                        <span><?= Html::encode($label) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="applicant-home-grid">
            <section class="card-p applicant-stage-list">
                <div class="applicant-section-heading">
                    <div><span class="eyebrow">RINGKASAN</span><h2>Ringkasan Pendaftaran</h2></div>
                    <?= Html::a('Buka formulir', ['form'], ['class' => 'applicant-text-link']) ?>
                </div>
                <div class="applicant-summary-grid">
                    <a class="applicant-summary-tile" href="<?= Html::encode(Url::to(['form'])) ?>">
                        <span class="applicant-summary-icon" aria-hidden="true">♙</span>
                        <span class="applicant-summary-title">Data Diri</span>
                        <strong><?= $pendaftaran->nama_lengkap && $pendaftaran->tanggal_lahir ? 'Lengkap' : 'Perlu dilengkapi' ?></strong>
                        <small><?= Html::encode($pendaftaran->tanggal_lahir ?: 'Lengkapi identitas pendaftar') ?></small>
                    </a>
                    <a class="applicant-summary-tile" href="<?= Html::encode(Url::to(['akademik'])) ?>">
                        <span class="applicant-summary-icon" aria-hidden="true">▤</span>
                        <span class="applicant-summary-title">Data Akademik</span>
                        <strong><?= $completedSteps >= 3 || $isSubmitted ? 'Tersimpan' : 'Perlu dilengkapi' ?></strong>
                        <small><?= Html::encode($pendaftaran->asal_institusi ?: 'Riwayat & nilai akademik') ?></small>
                    </a>
                    <a class="applicant-summary-tile" href="<?= Html::encode(Url::to(['dokumen'])) ?>">
                        <span class="applicant-summary-icon" aria-hidden="true">▣</span>
                        <span class="applicant-summary-title">Dokumen</span>
                        <strong><?= $documentsComplete ? 'Lengkap' : $uploadedCount . ' / ' . $requiredCount . ' berkas' ?></strong>
                        <small><?= $pendaftaran->no_paspor ? 'Paspor ' . Html::encode($pendaftaran->no_paspor) : 'Berkas persyaratan' ?></small>
                    </a>
                    <a class="applicant-summary-tile" href="<?= Html::encode(Url::to(['visa'])) ?>">
                        <span class="applicant-summary-icon" aria-hidden="true">▧</span>
                        <span class="applicant-summary-title">Bantuan Visa</span>
                        <strong><?= Html::encode($visaStatus) ?></strong>
                        <small><?= Html::encode($pendaftaran->jenis_visa ?: 'Layanan visa') ?></small>
                    </a>
                </div>
            </section>

            <aside class="applicant-home-aside">
                <section class="card-p applicant-action-card">
                    <span class="applicant-summary-icon" aria-hidden="true">✓</span>
                    <div>
                        <h2><?= $isSubmitted ? 'Pendaftaran terkirim' : 'Lanjutkan pendaftaran' ?></h2>
                        <p><?= $isSubmitted ? 'Pantau pembaruan status pendaftaran Anda.' : 'Lanjutkan dari tahap yang masih perlu dilengkapi.' ?></p>
                    </div>
                    <?= Html::a($isSubmitted ? 'Lihat Status' : 'Lanjutkan Formulir', $isSubmitted ? ['status'] : ['form'], ['class' => 'btn-primary']) ?>
                </section>
                <section class="card-p applicant-help-card">
                    <span class="eyebrow">LAYANAN ADMISI INTERNASIONAL</span>
                    <p>Perlu bantuan? Hubungi Kantor Urusan Internasional (KUI).</p>
                    <?= Html::a('Hubungi KUI →', ['bantuan'], ['class' => 'applicant-text-link']) ?>
                </section>
            </aside>
        </div>
    <?php endif; ?>
</div>
