<?php

use app\models\Pendaftaran;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Pendaftaran[] $registrations */
/** @var int $total */
/** @var int $allTimeTotal */
/** @var int $periodTotal */
/** @var int $auditIncomplete */
/** @var yii\data\Pagination $pagination */
/** @var string $year */
/** @var string $reportYear */
/** @var string $category */
/** @var string $search */
/** @var array<int, array{kategori_program: string|null, total: string|int}> $programBreakdown */
/** @var bool $print */

$this->title = 'Riwayat & Laporan Registrasi Akademik';
if ($print) {
    $this->registerJs("window.addEventListener('load', function () { window.print(); });");
}
$categoryLabels = [
    'degree' => 'Degree',
    'non-degree' => 'Non-degree',
];
$breakdownTotal = array_sum(array_map(static function (array $row): int {
    return (int) $row['total'];
}, $programBreakdown));
?>
<div class="academic-page academic-history">
    <nav class="academic-breadcrumb" aria-label="Breadcrumb">
        <?= Html::a('Dashboard Biro Akademik', ['/akademik/index']) ?>
        <span>›</span>
        <strong>Riwayat &amp; Laporan Penerbitan Registrasi</strong>
    </nav>
    <section class="academic-heading academic-history-heading">
        <div>
            <div class="academic-eyebrow">ARSIP BIRO PENDIDIKAN &amp; AKADEMIK</div>
            <h1>Riwayat Penerbitan &amp; Laporan Registrasi Akademik</h1>
            <p>Rekap nomor registrasi yang diterbitkan beserta tanggal dan petugas pencatatnya.</p>
        </div>
        <div class="academic-actions">
            <?= Html::a('Cetak Berita Acara', ['/akademik/riwayat', 'periode' => $year, 'kategori' => $category, 'q' => $search, 'print' => 1], ['class' => 'academic-button secondary']) ?>
            <?= Html::a('Cetak Laporan Semester (BA)', ['/akademik/riwayat', 'periode' => $reportYear, 'print' => 1], ['class' => 'academic-button']) ?>
        </div>
    </section>

    <section class="academic-stats academic-report-stats" aria-label="Ringkasan laporan registrasi">
        <article class="academic-stat">
            <span>Total registrasi tersimpan</span>
            <strong><?= Html::encode((string) $allTimeTotal) ?></strong>
            <small>Nomor yang tercatat pada portal</small>
        </article>
        <article class="academic-stat">
            <span>Terbit tahun <?= Html::encode($reportYear) ?></span>
            <strong><?= Html::encode((string) $periodTotal) ?></strong>
            <small>Seluruh kategori pada tahun laporan</small>
        </article>
        <article class="academic-stat">
            <span>Berkas pada daftar</span>
            <strong><?= Html::encode((string) $total) ?></strong>
            <small>Sesuai filter yang dipilih</small>
        </article>
        <article class="academic-stat <?= $auditIncomplete > 0 ? 'warning' : '' ?>">
            <span>Kelengkapan catatan</span>
            <strong><?= $auditIncomplete === 0 ? 'Lengkap' : Html::encode((string) $auditIncomplete . ' perlu cek') ?></strong>
            <small><?= $auditIncomplete === 0 ? 'Nomor, tanggal, dan petugas tersimpan' : 'Tanggal atau petugas penerbit belum tercatat' ?></small>
        </article>
    </section>

    <section class="academic-report-filter academic-card">
        <?= Html::beginForm(['/akademik/riwayat'], 'get', ['class' => 'academic-filter']) ?>
        <?= Html::input('number', 'periode', $year, ['placeholder' => 'Tahun akademik', 'min' => 2000, 'max' => date('Y') + 1, 'aria-label' => 'Filter tahun penerbitan']) ?>
        <?= Html::dropDownList('kategori', $category, ['' => 'Semua kategori', 'degree' => 'Degree', 'non-degree' => 'Non-degree'], ['aria-label' => 'Filter kategori program']) ?>
        <?= Html::textInput('q', $search, ['placeholder' => 'Cari nama atau nomor registrasi...', 'aria-label' => 'Cari riwayat registrasi']) ?>
        <?= Html::submitButton('Terapkan filter', ['class' => 'academic-button']) ?>
        <?= Html::a('Reset', ['/akademik/riwayat'], ['class' => 'academic-button secondary']) ?>
        <?= Html::endForm() ?>
    </section>

    <section class="academic-card academic-report-table-card">
        <div class="academic-report-table-heading">
            <div>
                <h2>Daftar Berkas &amp; Riwayat Registrasi</h2>
                <p>Nomor registrasi, mahasiswa, waktu penerbitan, dan petugas yang mencatat.</p>
            </div>
            <span class="academic-tag"><?= Html::encode((string) $total) ?> berkas</span>
        </div>
        <div class="academic-table-wrap">
            <table class="academic-table academic-report-table">
                <thead>
                    <tr><th>No</th><th>Nomor dokumen registrasi</th><th>Program / fakultas</th><th>Pengesahan catatan</th><th>Mahasiswa</th><th>Petugas penerbit</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                <?php if (!$registrations): ?>
                    <tr><td colspan="7" class="academic-muted">Belum ada arsip penerbitan untuk filter ini.</td></tr>
                <?php else: ?>
                    <?php foreach ($registrations as $index => $item): ?>
                        <tr>
                            <td><?= Html::encode((string) ($index + 1)) ?></td>
                            <td>
                                <strong><?= Html::encode((string) $item->registration_number) ?></strong>
                                <small class="academic-report-cell-note">Tersimpan di portal</small>
                            </td>
                            <td>
                                <?= Html::encode(ucwords((string) ($item->jenis_program ?: $item->kategori_program))) ?>
                                <small class="academic-report-cell-note"><?= Html::encode((string) ($item->fakultas->nama_fakultas ?? 'Fakultas belum ditentukan')) ?></small>
                            </td>
                            <td>
                                <span class="academic-tag <?= $item->registration_at && $item->registration_by ? 'success' : 'warning' ?>"><?= $item->registration_at && $item->registration_by ? 'Tercatat' : 'Perlu diperiksa' ?></span>
                                <small class="academic-report-cell-note"><?= $item->registration_at ? Html::encode(Yii::$app->formatter->asDatetime($item->registration_at)) : 'Waktu tidak tersedia' ?></small>
                            </td>
                            <td><strong><?= Html::encode((string) $item->nama_lengkap) ?></strong></td>
                            <td><?= Html::encode((string) ($item->registrationBy->nama ?? 'Belum tercatat')) ?></td>
                            <td><?= Html::a('Lihat', ['/akademik/penerbitan', 'id' => $item->pendaftaran_id], ['class' => 'academic-button secondary']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p class="academic-report-footnote">Menampilkan <?= $total > 0 ? Html::encode((string) ($pagination->offset + 1)) . '–' . Html::encode((string) ($pagination->offset + count($registrations))) : '0' ?> dari <?= Html::encode((string) $total) ?> rekaman yang sesuai filter.</p>
        <?= \yii\widgets\LinkPager::widget(['pagination' => $pagination, 'options' => ['class' => 'academic-pagination']]) ?>
    </section>

    <section class="academic-report-lower">
        <article class="academic-card">
            <div class="academic-report-panel-heading">
                <div><h2>Jejak Log Penerbitan</h2><p>Aktivitas berdasarkan data registrasi yang tersimpan.</p></div>
                <span aria-hidden="true">◷</span>
            </div>
            <div class="academic-audit">
                <?php if (!$registrations): ?>
                    <p class="academic-subtitle">Belum ada penerbitan pada filter ini.</p>
                <?php else: ?>
                    <?php foreach (array_slice($registrations, 0, 4) as $item): ?>
                        <div class="academic-audit-item">
                            <strong>Nomor registrasi diterbitkan</strong>
                            <small><?= Html::encode((string) $item->registration_number) ?> · <?= Html::encode((string) $item->nama_lengkap) ?><br><?= $item->registration_at ? Html::encode(Yii::$app->formatter->asDatetime($item->registration_at)) : 'Waktu tidak tersedia' ?> · <?= Html::encode((string) ($item->registrationBy->nama ?? 'Petugas belum tercatat')) ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="academic-report-log-note">Riwayat ini mencatat penerbitan yang tersimpan; bukan log aktivitas eksternal.</div>
        </article>

        <article class="academic-card">
            <div class="academic-report-panel-heading">
                <div><h2>Komposisi Registrasi</h2><p>Distribusi berdasarkan kategori program.</p></div>
                <span class="academic-tag success">Semua periode</span>
            </div>
            <?php if ($breakdownTotal > 0): ?>
                <div class="academic-report-breakdown" role="img" aria-label="Komposisi registrasi berdasarkan kategori program">
                    <?php foreach ($programBreakdown as $row): ?>
                        <?php
                        $rowCount = (int) $row['total'];
                        $rowCategory = (string) ($row['kategori_program'] ?? '');
                        $rowLabel = $categoryLabels[$rowCategory] ?? 'Kategori belum diisi';
                        $percentage = (int) round($rowCount / $breakdownTotal * 100);
                        ?>
                        <div class="academic-report-breakdown-row">
                            <div><span><?= Html::encode($rowLabel) ?></span><strong><?= Html::encode((string) $rowCount) ?> registrasi <small>(<?= Html::encode((string) $percentage) ?>%)</small></strong></div>
                            <div class="academic-report-meter"><span style="width:<?= Html::encode((string) $percentage) ?>%"></span></div>
                        </div>
                    <?php endforeach; ?>
                    <div class="academic-report-breakdown-total"><strong><?= Html::encode((string) $breakdownTotal) ?></strong><span>registrasi tercatat</span></div>
                </div>
            <?php else: ?>
                <p class="academic-subtitle academic-report-empty">Belum ada data penerbitan untuk diringkas.</p>
            <?php endif; ?>
        </article>

        <article class="academic-card academic-report-integrity">
            <div class="academic-report-panel-heading">
                <div><h2>Validasi Catatan</h2><p>Status kelengkapan data lokal.</p></div>
                <span aria-hidden="true">✓</span>
            </div>
            <p>Rekap ini berasal dari nomor, tanggal penerbitan, dan nama petugas yang tersimpan di portal. Integrasi feeder atau tanda tangan elektronik belum tersedia pada mode demo.</p>
            <div class="academic-report-integrity-status">
                <span class="academic-tag <?= $auditIncomplete === 0 ? 'success' : 'warning' ?>"><?= $auditIncomplete === 0 ? 'Catatan lengkap' : 'Perlu pemeriksaan' ?></span>
                <strong><?= Html::encode((string) $auditIncomplete) ?></strong>
                <small>rekaman dengan tanggal atau petugas belum tercatat</small>
            </div>
        </article>
    </section>

    <section class="academic-print-only"><small>Dicetak dari Portal Biro Pendidikan &amp; Akademik UNAND · <?= Html::encode(Yii::$app->formatter->asDatetime(date('Y-m-d H:i:s'))) ?></small></section>
</div>
