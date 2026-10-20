<?php

use app\models\Pendaftaran;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Pendaftaran[] $pendaftar */
/** @var int $total */
/** @var int $lolos */
/** @var string $search */

$this->title = 'Riwayat Seleksi UPT PDK';
?>
<div class="pdk-page">
    <section class="pdk-heading pdk-card">
        <div>
            <div class="pdk-eyebrow">ARSIP KEPUTUSAN & DOKUMEN RESMI</div>
            <h1>Riwayat Seleksi UPT PDK</h1>
            <p>Daftar arsip keputusan dan catatan seleksi pendaftar WNI yang pernah ditangani UPT PDK.</p>
        </div>
        <?= Html::a('Cetak halaman', '#', ['class' => 'pdk-button secondary', 'onclick' => 'window.print(); return false;']) ?>
    </section>
    <section class="pdk-stats">
        <article class="pdk-stat"><span>Total berkas diarsipkan</span><strong><?= Html::encode((string) $total) ?></strong><small>Rekaman hasil seleksi</small></article>
        <article class="pdk-stat"><span>Direkomendasikan lolos</span><strong><?= Html::encode((string) $lolos) ?></strong><small>Termasuk pendaftar yang diteruskan</small></article>
        <article class="pdk-stat"><span>Arsip keputusan</span><strong><?= Html::encode((string) $total) ?></strong><small>Keputusan dan catatan terdokumentasi</small></article>
        <article class="pdk-stat"><span>Periode aktif</span><strong>2026/27</strong><small>Tahun akademik</small></article>
    </section>
    <section class="pdk-card">
        <?= Html::beginForm(['/pdk/riwayat'], 'get', ['class' => 'pdk-filter-row']) ?>
        <?= Html::textInput('q', $search, ['placeholder' => 'Cari nama, nomor registrasi, atau program...', 'aria-label' => 'Cari arsip']) ?>
        <?= Html::submitButton('Cari', ['class' => 'pdk-button']) ?>
        <?= Html::a('Reset', ['/pdk/riwayat'], ['class' => 'pdk-button secondary']) ?>
        <?= Html::endForm() ?>
        <div class="pdk-table-wrap" style="margin-top:14px">
            <table class="pdk-table">
                <thead><tr><th>No</th><th>Nama & nomor pendaftaran</th><th>Program & fakultas</th><th>Asal institusi</th><th>Keputusan</th><th>Catatan evaluasi</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php if (!$pendaftar): ?>
                    <tr><td colspan="7" class="pdk-muted">Belum ada arsip keputusan seleksi.</td></tr>
                <?php else: ?>
                    <?php foreach ($pendaftar as $index => $item): ?>
                        <?php $approved = in_array($item->status, [Pendaftaran::STATUS_LOLOS, Pendaftaran::STATUS_REGISTRATION_NUMBER_TERBIT], true); ?>
                        <tr>
                            <td><?= Html::encode((string) ($index + 1)) ?></td>
                            <td><strong><?= Html::encode((string) $item->nama_lengkap) ?></strong><br><small class="pdk-muted">PDK-<?= Html::encode((string) $item->pendaftaran_id) ?></small></td>
                            <td><?= Html::encode(ucwords((string) ($item->jenis_program ?: $item->kategori_program))) ?><br><small class="pdk-muted"><?= Html::encode((string) ($item->fakultas->nama_fakultas ?? 'Fakultas belum ditetapkan')) ?></small></td>
                            <td><?= Html::encode((string) ($item->asal_institusi ?: '—')) ?></td>
                            <td><span class="pdk-badge <?= $approved ? 'success' : 'danger' ?>"><?= Html::encode((string) $item->status) ?></span></td>
                            <td><?= Html::encode((string) ($item->catatan_keputusan ?: 'Tidak ada catatan')) ?></td>
                            <td><?= Html::a('Lihat', ['/pdk/evaluasi', 'id' => $item->pendaftaran_id], ['class' => 'pdk-button secondary']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
    <section class="pdk-alert"><strong>Retensi arsip keputusan</strong><br>Catatan evaluasi disimpan sebagai bagian dari riwayat seleksi untuk mendukung penelusuran dan audit internal.</section>
</div>
