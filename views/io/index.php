<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array[] $pendaftar */
/** @var int $totalPendaftar */
/** @var int $menungguVerifikasi */
/** @var int $permohonanVisa */
/** @var int $dokumenAktif */

$this->title = 'Ringkasan International Office';
?>
<?= $this->render('_demo_notice') ?>
<section class="io-welcome">
    <div>
        <div class="eyebrow">KANTOR URUSAN INTERNASIONAL (IO) UNAND</div>
        <h1>Selamat Datang, Tim Admisi Internasional</h1>
        <p class="muted">Pusat kendali admisi mahasiswa asing, verifikasi dokumen, dan bantuan pengurusan visa.</p>
    </div>
    <div class="io-period"><span></span> Periode Aktif<strong>Tahun Akademik 2026/2027</strong></div>
</section>

<section class="io-stat-grid" aria-label="Ringkasan data International Office">
    <a class="io-stat-card" href="<?= Html::encode(Url::to(['/io/verifikasi'])) ?>">
        <span>Total pendaftar WNA</span><strong><?= Html::encode((string) $totalPendaftar) ?></strong>
        <small>Seluruh pendaftaran yang sudah dikirim</small>
    </a>
    <a class="io-stat-card io-stat-warn" href="<?= Html::encode(Url::to(['/io/verifikasi', 'status' => 'Terkirim'])) ?>">
        <span>Menunggu verifikasi</span><strong><?= Html::encode((string) $menungguVerifikasi) ?></strong>
        <small>Pendaftaran perlu ditinjau</small>
    </a>
    <a class="io-stat-card" href="<?= Html::encode(Url::to(['/io/bantuan-visa'])) ?>">
        <span>Bantuan visa aktif</span><strong><?= Html::encode((string) $permohonanVisa) ?></strong>
        <small>Permohonan belum selesai</small>
    </a>
    <a class="io-stat-card" href="<?= Html::encode(Url::to(['/io/dokumen-persyaratan'])) ?>">
        <span>Dokumen persyaratan aktif</span><strong><?= Html::encode((string) $dokumenAktif) ?></strong>
        <small>Ketentuan pendaftaran saat ini</small>
    </a>
</section>

<section class="io-panel">
    <div class="io-panel-heading">
        <div>
            <h2>Pendaftaran Masuk Terbaru (WNA)</h2>
            <p class="muted">Tinjau data pendaftar asing dan kelengkapan dokumen pendaftaran.</p>
        </div>
        <?= Html::a('Lihat semua pendaftaran', ['/io/verifikasi'], ['class' => 'io-button io-button-light']) ?>
    </div>
    <?php if ($pendaftar): ?>
        <div class="io-table-wrap">
            <table class="io-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Jalur &amp; kategori</th>
                    <th>Program studi &amp; fakultas</th>
                    <th>Tanggal kirim</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($pendaftar as $applicant): ?>
                    <tr>
                        <td>#<?= Html::encode(str_pad((string) $applicant['pendaftaran_id'], 4, '0', STR_PAD_LEFT)) ?></td>
                        <td>
                            <strong><?= Html::encode($applicant['nama_lengkap']) ?></strong>
                            <small><?= Html::encode($applicant['asal_negara']) ?></small>
                        </td>
                        <td>
                            <span class="io-tag"><?= Html::encode(strtoupper($applicant['jalur'])) ?></span>
                            <small><?= Html::encode(ucfirst($applicant['kategori_program'])) ?></small>
                        </td>
                        <td>
                            <strong><?= Html::encode($applicant['jenjang'] ?: $applicant['jenis_program']) ?></strong>
                            <small><?= Html::encode($applicant['fakultas']) ?></small>
                        </td>
                        <td><?= Html::encode(Yii::$app->formatter->asDate($applicant['submitted_at'], 'php:d M Y')) ?></td>
                        <td><span class="io-status"><?= Html::encode($applicant['status']) ?></span></td>
                        <td><?= Html::a('Periksa', ['/io/detail-verifikasi', 'id' => $applicant['pendaftaran_id']], ['class' => 'io-button io-button-primary io-button-small']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="io-empty">Belum ada pendaftaran mahasiswa asing yang dikirim.</div>
    <?php endif; ?>
</section>
