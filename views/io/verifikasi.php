<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array[] $pendaftar */
/** @var string $search */
/** @var string $statusFilter */

$this->title = 'Verifikasi Pendaftaran Internasional';
$statuses = [
    '' => 'Semua status',
    'Terkirim' => 'Terkirim',
    'Lolos' => 'Lolos',
    'Tidak lolos' => 'Tidak lolos',
    'Registration number terbit' => 'Registration number terbit',
];
?>
<?= $this->render('_demo_notice') ?>
<div class="io-breadcrumb">INTERNATIONAL OFFICE <span>›</span> VERIFIKASI PENDAFTARAN</div>
<section class="io-page-heading">
    <div>
        <h1>Verifikasi Pendaftaran Internasional</h1>
        <p class="muted">Periksa identitas, program studi, dan dokumen pendaftar sebelum menetapkan keputusan.</p>
    </div>
    <span class="io-heading-badge"><?= Html::encode((string) count($pendaftar)) ?> pendaftaran</span>
</section>

<section class="io-panel">
    <form class="io-filter" method="get" action="<?= Html::encode(Url::to(['/io/verifikasi'])) ?>">
        <label class="io-search">
            <span aria-hidden="true">⌕</span>
            <input type="search" name="q" value="<?= Html::encode($search) ?>" placeholder="Cari nama atau nomor paspor..." aria-label="Cari nama atau nomor paspor">
        </label>
        <select name="status" aria-label="Filter status">
            <?php foreach ($statuses as $value => $label): ?>
                <option value="<?= Html::encode($value) ?>" <?= $statusFilter === $value ? 'selected' : '' ?>><?= Html::encode($label) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="io-button io-button-primary" type="submit">Terapkan filter</button>
    </form>
    <?php if ($pendaftar): ?>
        <div class="io-table-wrap">
            <table class="io-table">
                <thead>
                <tr>
                    <th>ID</th><th>Nama</th><th>Jalur &amp; kategori</th>
                    <th>Program studi &amp; fakultas</th><th>Tanggal kirim</th><th>Status</th><th>Aksi</th>
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
        <div class="io-empty">Tidak ada pendaftaran yang cocok dengan pencarian ini.</div>
    <?php endif; ?>
</section>
