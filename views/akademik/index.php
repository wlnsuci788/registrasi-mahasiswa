<?php

use app\models\Pendaftaran;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var Pendaftaran[] $registrations */
/** @var int $total */
/** @var int $pending */
/** @var int $issued */
/** @var int $needsReview */
/** @var string $search */

$this->title = 'Dashboard Biro Akademik';
?>
<div class="academic-page">
    <section class="academic-heading">
        <div>
            <div class="academic-eyebrow">BIRO PENDIDIKAN &amp; AKADEMIK · REGISTRATION NUMBER</div>
            <h1>Dashboard Biro Akademik – Penerbitan Registration Number</h1>
            <p class="academic-subtitle">Pantau verifikasi akhir hasil seleksi dan terbitkan nomor registrasi untuk pendaftar yang sudah dinyatakan lolos.</p>
        </div>
        <div class="academic-actions">
            <span class="academic-tag">TA <?= Html::encode(date('Y') . '/' . (date('Y') + 1)) ?></span>
            <?= Html::a('Ekspor / cetak laporan', ['/akademik/riwayat', 'print' => 1], ['class' => 'academic-button secondary']) ?>
            <?= Html::a('Buku registrasi induk', ['/akademik/buku'], ['class' => 'academic-button']) ?>
        </div>
    </section>

    <section class="academic-banner">
        <strong>Mandat Penerbitan Nomor Induk &amp; Registrasi</strong>
        <p>Biro Akademik memverifikasi kelayakan data sebelum menerbitkan nomor registrasi. Nomor yang diterbitkan tercatat pada sistem sebagai data demo dan belum disinkronkan ke SIAKAD eksternal.</p>
    </section>

    <section class="academic-stats">
        <article class="academic-stat"><span>Disetujui fakultas &amp; unit seleksi</span><strong><?= Html::encode((string) $total) ?></strong><small>Pendaftar yang lolos verifikasi</small></article>
        <article class="academic-stat warning"><span>Menunggu terbit nomor</span><strong><?= Html::encode((string) $pending) ?></strong><small>Antrean berkas di Biro Akademik</small></article>
        <article class="academic-stat"><span>Registration number terbit</span><strong><?= Html::encode((string) $issued) ?></strong><small>Nomor registrasi tersimpan</small></article>
        <article class="academic-stat danger"><span>Perlu klarifikasi</span><strong><?= Html::encode((string) $needsReview) ?></strong><small>Pendaftar dengan status tidak lolos</small></article>
    </section>

    <section class="academic-card">
        <div class="academic-heading">
            <div><h2 style="font-size:15px;margin:0">Progres alur kerja registrasi terpadu</h2><p class="academic-subtitle">Tahapan verifikasi hingga penerbitan nomor</p></div>
        </div>
        <div class="academic-progress">
            <article class="academic-progress-step"><span>01 · Seleksi selesai</span><strong>Validasi hasil pleno</strong><p>Pastikan keputusan Lolos telah disimpan oleh fakultas atau unit seleksi.</p></article>
            <article class="academic-progress-step active"><span>02 · Tahap aktif</span><strong>Alokasi &amp; penerbitan nomor</strong><p>Periksa data pendaftar dan terbitkan nomor registrasi yang unik.</p></article>
            <article class="academic-progress-step"><span>03 · Tahap menunggu</span><strong>Kredensial SIAKAD</strong><p>Persiapan akun mahasiswa ditangani melalui prosedur akademik.</p></article>
            <article class="academic-progress-step"><span>04 · Tahap menunggu</span><strong>SK registrasi &amp; arsip</strong><p>Rekapitulasi penerbitan dapat dicetak dari menu Riwayat &amp; Laporan.</p></article>
        </div>
    </section>

    <section class="academic-card">
        <div class="academic-heading">
            <div><h2 style="font-size:15px;margin:0">Antrean penerbitan nomor registrasi</h2><p class="academic-subtitle">Hasil seleksi yang lolos dan nomor yang sudah diterbitkan</p></div>
        </div>
        <?= Html::beginForm(['/akademik/index'], 'get', ['class' => 'academic-filter', 'style' => 'margin:12px 0']) ?>
        <?= Html::textInput('q', $search, ['placeholder' => 'Cari nama, ID, atau nomor registrasi...', 'aria-label' => 'Cari pendaftar']) ?>
        <?= Html::submitButton('Cari', ['class' => 'academic-button']) ?>
        <?= Html::a('Reset', ['/akademik/index'], ['class' => 'academic-button secondary']) ?>
        <?= Html::endForm() ?>
        <div class="academic-table-wrap">
            <table class="academic-table">
                <thead><tr><th>No</th><th>Data pendaftar</th><th>Program &amp; fakultas</th><th>Asal institusi</th><th>Nomor registrasi</th><th>Status validasi</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php if (!$registrations): ?>
                    <tr><td colspan="7" class="academic-muted">Belum ada pendaftar yang lolos seleksi atau nomor registrasi yang diterbitkan.</td></tr>
                <?php else: ?>
                    <?php foreach ($registrations as $index => $item): ?>
                        <?php $isIssued = $item->status === Pendaftaran::STATUS_REGISTRATION_NUMBER_TERBIT; ?>
                        <tr>
                            <td><?= Html::encode((string) ($index + 1)) ?></td>
                            <td><strong><?= Html::encode((string) $item->nama_lengkap) ?></strong><br><small class="academic-muted">ID: <?= Html::encode((string) $item->pendaftaran_id) ?> · <?= Html::encode(strtoupper((string) $item->jalur)) ?></small></td>
                            <td><?= Html::encode(ucwords((string) ($item->jenis_program ?: $item->kategori_program))) ?><br><small class="academic-muted"><?= Html::encode((string) ($item->fakultas->nama_fakultas ?? 'Fakultas belum ditetapkan')) ?></small></td>
                            <td><?= Html::encode((string) ($item->asal_institusi ?: '—')) ?></td>
                            <td><?= $item->registration_number ? '<span class="academic-tag success">' . Html::encode((string) $item->registration_number) . '</span>' : '<span class="academic-tag warning">Menunggu generate</span>' ?></td>
                            <td><span class="academic-tag <?= $isIssued ? 'success' : 'warning' ?>"><?= Html::encode((string) $item->status) ?></span></td>
                            <td><?= Html::a($isIssued ? 'Lihat detail' : 'Penerbitan', ['/akademik/penerbitan', 'id' => $item->pendaftaran_id], ['class' => 'academic-button ' . ($isIssued ? 'secondary' : '')]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="academic-grid-2">
        <article class="academic-card">
            <?= Html::img(Url::to('@web/images/unand-campus-registration.png'), [
                'alt' => 'Gedung kampus Universitas Andalas Limau Manis',
                'class' => 'academic-campus-photo',
            ]) ?>
            <h2 style="font-size:14px;margin:10px 0 4px">Biro Pendidikan &amp; Akademik Limau Manis</h2>
            <p class="academic-subtitle">Pengelolaan data registrasi dan dokumen akademik Universitas Andalas.</p>
        </article>
        <article class="academic-card">
            <h2 style="font-size:14px;margin:0 0 5px">Integrasi data akademik</h2>
            <p class="academic-subtitle">Status penerbitan dicatat di portal demo. Sinkronisasi eksternal belum dilakukan.</p>
            <div class="academic-integration" style="margin-top:12px">
                <div class="academic-integration-item"><strong>Nomor registrasi</strong><span>Dibuat unik per pendaftaran dan disimpan bersama akun petugas penerbit.</span></div>
                <div class="academic-integration-item"><strong>Validasi data</strong><span>Periksa nama, program, fakultas, dan hasil seleksi sebelum menerbitkan.</span></div>
            </div>
            <p style="margin:12px 0 0"><?= Html::a('Buka buku registrasi →', ['/akademik/buku'], ['class' => 'academic-button light']) ?></p>
        </article>
    </section>
</div>
