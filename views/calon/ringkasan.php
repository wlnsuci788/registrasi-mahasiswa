<?php

use app\models\DokumenPendaftar;
use app\models\Pendaftaran;
use app\models\PengajuanVisa;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Pendaftaran $pendaftaran */
/** @var app\models\Fakultas|null $fakultas */
/** @var DokumenPendaftar[] $documents */
/** @var PengajuanVisa|null $visa */
/** @var string[] $submissionErrors */
/** @var int $activeStep */
/** @var bool $readOnly */

$this->title = 'Ringkasan & Kirim';
$namaFakultas = $fakultas ? $fakultas->nama_fakultas : 'Belum dipilih';
$visaData = $visa && $visa->data_pengajuan ? json_decode($visa->data_pengajuan, true) : [];
$visaChoiceLabel = match ($pendaftaran->pilihan_visa) {
    Pendaftaran::PILIHAN_VISA_YA => 'Meminta bantuan pengurusan visa dari International Office',
    Pendaftaran::PILIHAN_VISA_TIDAK => 'Mengurus visa secara mandiri',
    default => 'Belum dipilih',
};
?>

<div class="cal-dashboard registration-dashboard">
    <?= $this->render('_steps', ['activeStep' => $activeStep, 'readOnly' => $readOnly]) ?>
    <?= $this->render('_readonly_notice', ['readOnly' => $readOnly]) ?>
    <div class="registration-grid">
        <div class="registration-main">
    <div class="cal-greeting">
        <div>
            <div class="eyebrow">TAHAP 6 DARI 6</div>
            <h1>Periksa &amp; Kirim Pendaftaran</h1>
            <p class="muted">Pastikan data sudah benar sebelum dikirim untuk proses verifikasi.</p>
        </div>
    </div>

    <section class="card-p review-section">
        <h2>Data Diri</h2>
        <div class="review-grid">
            <div><span>Nama</span><strong><?= Html::encode($pendaftaran->nama_lengkap ?: 'Belum diisi') ?></strong></div>
            <div><span>Jalur</span><strong><?= Html::encode($pendaftaran->jalur ?: 'Belum dipilih') ?></strong></div>
            <div><span>Tempat, tanggal lahir</span><strong><?= Html::encode(trim(($pendaftaran->tempat_lahir ?: '') . ', ' . ($pendaftaran->tanggal_lahir ?: ''), ', ')) ?></strong></div>
            <div><span>Negara asal</span><strong><?= Html::encode($pendaftaran->asal_negara ?: 'Belum diisi') ?></strong></div>
            <div><span>Nomor paspor</span><strong><?= Html::encode($pendaftaran->no_paspor ?: 'Belum diisi') ?></strong></div>
        </div>
        <?= Html::a($readOnly ? 'Lihat Data Diri' : 'Ubah Data Diri', ['form'], ['class' => 'review-edit']) ?>
    </section>

    <section class="card-p review-section">
        <h2>Pilihan Program &amp; Akademik</h2>
        <div class="review-grid">
            <div><span>Kategori</span><strong><?= Html::encode($pendaftaran->kategori_program ?: 'Belum dipilih') ?></strong></div>
            <div><span>Program / jenjang</span><strong><?= Html::encode($pendaftaran->jenjang ?: $pendaftaran->jenis_program ?: 'Belum dipilih') ?></strong></div>
            <div><span>Fakultas</span><strong><?= Html::encode($namaFakultas) ?></strong></div>
            <div><span>Institusi asal</span><strong><?= Html::encode($pendaftaran->asal_institusi ?: 'Belum diisi') ?></strong></div>
            <div><span>Nilai akhir</span><strong><?= Html::encode((string) ($pendaftaran->nilai_akhir ?? 'Belum diisi')) ?></strong></div>
            <div><span>Pembiayaan</span><strong><?= Html::encode($pendaftaran->pembiayaan ?: 'Belum dipilih') ?></strong></div>
        </div>
        <?= Html::a($readOnly ? 'Lihat Pilihan Program' : 'Ubah Pilihan Program', ['program'], ['class' => 'review-edit']) ?>
        <?= Html::a($readOnly ? 'Lihat Data Akademik' : 'Ubah Data Akademik', ['akademik'], ['class' => 'review-edit']) ?>
    </section>

    <section class="card-p review-section">
        <h2>Dokumen &amp; Bantuan Visa</h2>
        <ul class="review-documents">
            <?php foreach ($documents as $document): ?>
                <li>
                    <span><?= Html::encode($document->dokumen ? $document->dokumen->nama_dokumen : 'Dokumen') ?></span>
                    <?= Html::a(
                        Html::encode($document->nama_file ?: 'Unduh berkas'),
                        ['unduh-dokumen', 'id' => $document->dokumen_pendaftar_id],
                        [
                            'class' => 'review-document-download',
                            'aria-label' => 'Unduh ' . ($document->dokumen ? $document->dokumen->nama_dokumen : 'dokumen'),
                        ],
                    ) ?>
                </li>
            <?php endforeach; ?>
            <?php if (!$documents): ?>
                <li class="muted">Belum ada dokumen yang diunggah.</li>
            <?php endif; ?>
        </ul>
        <p><b>Pilihan pengurusan visa:</b> <?= Html::encode($visaChoiceLabel) ?></p>
        <?php if ($visaData): ?>
            <p class="muted">Tujuan: <?= Html::encode($visaData['tujuan'] ?? '') ?> · Kedatangan: <?= Html::encode($visaData['tanggal_kedatangan'] ?? '') ?></p>
        <?php endif; ?>
        <?= Html::a($readOnly ? 'Lihat Dokumen' : 'Ubah Dokumen', ['dokumen'], ['class' => 'review-edit']) ?>
        <?= Html::a($readOnly ? 'Lihat Bantuan Visa' : 'Ubah Bantuan Visa', ['visa'], ['class' => 'review-edit']) ?>
    </section>

    <?php if ($readOnly): ?>
        <section class="card-p registration-readonly-confirm">
            <strong>Pendaftaran sudah dikirim</strong>
            <p class="muted">Halaman ini hanya untuk pemeriksaan. Anda dapat membuka tahap lain dari navigasi tahapan di atas.</p>
            <?= Html::a('Lihat Status Pendaftaran', ['status'], ['class' => 'btn-primary']) ?>
        </section>
    <?php elseif ($submissionErrors): ?>
        <section class="card-p form-notice">
            <strong>Masih ada yang perlu dilengkapi:</strong>
            <ul>
                <?php foreach ($submissionErrors as $error): ?>
                    <li><?= Html::encode($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php else: ?>
        <?= Html::beginForm(['ringkasan'], 'post', ['class' => 'card-p submit-confirm']) ?>
        <label>
            <?= Html::checkbox('integrity', false, ['required' => true]) ?>
            Saya menyatakan bahwa data dan dokumen yang saya berikan benar dan dapat dipertanggungjawabkan.
        </label>
        <?= Html::submitButton('Kirim Pendaftaran', ['class' => 'btn-primary']) ?>
        <?= Html::endForm() ?>
    <?php endif; ?>

    <div class="candidate-form-actions">
        <?= Html::a('← Kembali ke Bantuan Visa', ['visa'], ['class' => 'btn-secondary']) ?>
    </div>
        </div>
        <?= $this->render('_registration_aside', ['pendaftaran' => $pendaftaran, 'activeStep' => $activeStep]) ?>
    </div>
</div>
