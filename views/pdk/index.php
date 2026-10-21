<?php

use app\models\Pendaftaran;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var Pendaftaran[] $pendaftar */
/** @var int $total */
/** @var int $menunggu */
/** @var int $lolos */
/** @var int $tidakLolos */

$this->title = 'Dashboard UPT Pendidikan Luar Kampus';
?>
<div class="pdk-page">
    <section class="pdk-heading pdk-card">
        <div>
            <div class="pdk-eyebrow">PORTAL VERIFIKATOR RESMI · UPT PDK</div>
            <h1>Dashboard UPT Pendidikan Luar Kampus (PDK)</h1>
            <p>Pengelolaan dan seleksi pendaftar WNI program BIPA dan Credit Earning non-degree.</p>
        </div>
        <div class="pdk-actions">
            <?= Html::a('Buka daftar seleksi', ['/pdk/pendidikan'], ['class' => 'pdk-button']) ?>
            <?= Html::a('Riwayat seleksi', ['/pdk/riwayat'], ['class' => 'pdk-button secondary']) ?>
        </div>
    </section>

    <section class="pdk-stats">
        <article class="pdk-stat"><span>Total berkas WNI masuk</span><strong><?= Html::encode((string) $total) ?></strong><small>Pendaftar yang masuk antrean UPT PDK</small></article>
        <article class="pdk-stat"><span>Menunggu evaluasi</span><strong><?= Html::encode((string) $menunggu) ?></strong><small>Perlu ditelaah oleh petugas</small></article>
        <article class="pdk-stat"><span>Direkomendasikan lolos</span><strong><?= Html::encode((string) $lolos) ?></strong><small>Keputusan seleksi tersimpan</small></article>
        <article class="pdk-stat"><span>Tidak direkomendasikan</span><strong><?= Html::encode((string) $tidakLolos) ?></strong><small>Keputusan disertai catatan</small></article>
    </section>

    <section class="pdk-alert">
        <strong>Informasi Penugasan Seleksi UPT PDK</strong><br>
        UPT PDK memeriksa kelengkapan berkas, kesesuaian akademik, dan rekomendasi untuk pendaftar WNI pada program BIPA serta Credit Earning.
    </section>

    <section class="pdk-grid-2">
        <div class="pdk-card">
            <div class="pdk-heading">
                <div><h2 style="font-size:16px;margin:0">Progres alur seleksi</h2><p>Langkah kerja verifikator</p></div>
            </div>
            <div class="pdk-program-grid" style="margin-top:12px">
                <div class="pdk-program-card"><span class="pdk-badge success">01 · Pendaftar diterima</span><h3>Periksa identitas</h3><p>Pastikan data diri dan jalur WNI terisi dengan benar.</p></div>
                <div class="pdk-program-card"><span class="pdk-badge">02 · Evaluasi aktif</span><h3>Evaluasi berkas</h3><p>Telaah dokumen persyaratan, asal kampus, dan program yang dipilih.</p></div>
                <div class="pdk-program-card"><span class="pdk-badge">03 · Rekomendasi</span><h3>Simpan keputusan</h3><p>Catat rekomendasi lolos, revisi, atau tidak lolos beserta alasannya.</p></div>
                <div class="pdk-program-card"><span class="pdk-badge">04 · Arsip</span><h3>Pantau riwayat</h3><p>Keputusan tersimpan pada riwayat seleksi dan dapat ditinjau kembali.</p></div>
            </div>
        </div>
        <div class="pdk-card">
            <div class="pdk-eyebrow">FASILITAS KAMPUS LIMAU MANIS</div>
            <figure class="pdk-campus-figure">
                <?= Html::img(Url::to('@web/images/unand-campus-registration.png'), [
                    'alt' => 'Gedung kampus Universitas Andalas Limau Manis',
                    'class' => 'pdk-campus-image',
                ]) ?>
                <figcaption>Universitas Andalas · Padang, Sumatera Barat</figcaption>
            </figure>
            <h2 class="pdk-campus-title">Kampus Limau Manis</h2>
            <p class="pdk-subtitle">Pusat kegiatan akademik dan program Pendidikan di Luar Kampus UNAND.</p>
            <p class="pdk-campus-link"><?= Html::a('Lihat katalog BIPA & Credit Earning →', ['/pdk/bipa'], ['class' => 'pdk-button light']) ?></p>
        </div>
    </section>

    <section class="pdk-card">
        <div class="pdk-heading">
            <div><h2 style="font-size:16px;margin:0">Antrean pendaftar WNI</h2><p>Data yang menunggu evaluasi UPT PDK</p></div>
            <?= Html::a('Lihat semua →', ['/pdk/pendidikan'], ['class' => 'pdk-button secondary']) ?>
        </div>
        <div class="pdk-table-wrap" style="margin-top:12px">
            <table class="pdk-table">
                <thead><tr><th>No</th><th>Nama pendaftar</th><th>Asal institusi</th><th>Program</th><th>Status seleksi</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php if (!$pendaftar): ?>
                    <tr><td colspan="6" class="pdk-muted">Belum ada pendaftar WNI yang menunggu evaluasi.</td></tr>
                <?php else: ?>
                    <?php foreach ($pendaftar as $index => $item): ?>
                        <?php $needsRevision = $item->keputusan_at !== null; ?>
                        <tr>
                            <td><?= Html::encode((string) ($index + 1)) ?></td>
                            <td><strong><?= Html::encode((string) $item->nama_lengkap) ?></strong><br><small class="pdk-muted">ID PDK-<?= Html::encode((string) $item->pendaftaran_id) ?></small></td>
                            <td><?= Html::encode((string) ($item->asal_institusi ?: 'Belum diisi')) ?></td>
                            <td><?= Html::encode(ucwords((string) ($item->jenis_program ?: $item->kategori_program))) ?></td>
                            <td><span class="pdk-badge"><?= $needsRevision ? 'Revisi diminta' : 'Menunggu evaluasi' ?></span></td>
                            <td><?= Html::a($needsRevision ? 'Lanjut evaluasi' : 'Mulai evaluasi', ['/pdk/evaluasi', 'id' => $item->pendaftaran_id], ['class' => 'pdk-button']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
