<?php

use app\models\Pendaftaran;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var Pendaftaran[] $pendaftar */
/** @var string $search */

$this->title = 'Pendidikan Luar Kampus';
?>
<div class="pdk-page">
    <section class="pdk-heading pdk-card">
        <div>
            <div class="pdk-eyebrow">SELEKSI PROGRAM WNI · UPT PDK</div>
            <h1>Pendidikan Luar Kampus</h1>
            <p>Periksa berkas dan kelayakan pendaftar WNI program non-degree secara bertahap.</p>
        </div>
        <?= Html::a('← Dashboard', ['/pdk/index'], ['class' => 'pdk-button secondary']) ?>
    </section>
    <section class="pdk-card">
        <?= Html::beginForm(['/pdk/pendidikan'], 'get', ['class' => 'pdk-filter-row']) ?>
        <?= Html::textInput('q', $search, ['placeholder' => 'Cari nama, institusi, atau program...', 'aria-label' => 'Cari pendaftar']) ?>
        <?= Html::submitButton('Cari', ['class' => 'pdk-button']) ?>
        <?= Html::a('Reset', ['/pdk/pendidikan'], ['class' => 'pdk-button secondary']) ?>
        <?= Html::endForm() ?>
        <div class="pdk-table-wrap" style="margin-top:14px">
            <table class="pdk-table">
                <thead><tr><th>No</th><th>Nama pendaftar</th><th>Program pilihan</th><th>Asal institusi</th><th>Tanggal masuk</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php if (!$pendaftar): ?>
                    <tr><td colspan="7" class="pdk-muted">Tidak ada data pendaftar yang cocok dengan pencarian.</td></tr>
                <?php else: ?>
                    <?php foreach ($pendaftar as $index => $item): ?>
                        <?php $needsRevision = $item->status === Pendaftaran::STATUS_TERKIRIM && $item->keputusan_at !== null; ?>
                        <tr>
                            <td><?= Html::encode((string) ($index + 1)) ?></td>
                            <td><strong><?= Html::encode((string) $item->nama_lengkap) ?></strong><br><small class="pdk-muted">ID PDK-<?= Html::encode((string) $item->pendaftaran_id) ?></small></td>
                            <td><?= Html::encode(ucwords((string) ($item->jenis_program ?: $item->kategori_program))) ?></td>
                            <td><?= Html::encode((string) ($item->asal_institusi ?: 'Belum diisi')) ?></td>
                            <td><?= $item->submitted_at ? Html::encode(Yii::$app->formatter->asDate($item->submitted_at)) : '—' ?></td>
                            <td><span class="pdk-badge"><?= Html::encode($needsRevision ? 'Revisi diminta' : (string) $item->status) ?></span></td>
                            <td><?= Html::a($item->status === Pendaftaran::STATUS_TERKIRIM ? ($needsRevision ? 'Lanjut evaluasi' : 'Evaluasi') : 'Lihat detail', ['/pdk/evaluasi', 'id' => $item->pendaftaran_id], ['class' => 'pdk-button']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p class="pdk-muted" style="margin:12px 0 0;font-size:10px">Menampilkan <?= count($pendaftar) ?> pendaftar pada antrean seleksi.</p>
    </section>
</div>
