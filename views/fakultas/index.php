<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array[] $applicants */
/** @var string $search */
/** @var int $totalApplicants */
/** @var int $pendingApplicants */
/** @var int $evaluatedApplicants */
/** @var string $evaluatorName */

$this->title = 'Dashboard Fakultas Kedokteran';
?>
<div class="faculty-dashboard">
<section class="faculty-welcome">
    <div>
        <span class="faculty-kicker">UNIT VERIFIKATOR FAKULTAS <span>›</span> Portal Seleksi PMB Asing</span>
        <h1>Dashboard Fakultas Kedokteran</h1>
        <p>Pusat pemantauan antrean pendaftar internasional Fakultas Kedokteran UNAND T.A. 2026/2027</p>
    </div>
    <div class="faculty-selected">
        <span class="faculty-selected-icon">✚</span>
        <div><small>FAKULTAS TERPILIH</small><strong>Kedokteran (FK)</strong><span>Kampus Limau Manis, Padang</span></div>
    </div>
</section>

<section class="faculty-stats">
    <article class="faculty-stat"><span>TOTAL ANTREAN FK</span><strong><?= Html::encode((string) $totalApplicants) ?></strong><small>▣ Dialokasikan ke FK UNAND</small></article>
    <article class="faculty-stat faculty-stat-wait"><span>MENUNGGU EVALUASI</span><strong><?= Html::encode((string) $pendingApplicants) ?></strong><small>◉ Prioritas verifikator</small></article>
    <article class="faculty-stat faculty-stat-done"><span>SELESAI DIEVALUASI</span><strong><?= Html::encode((string) $evaluatedApplicants) ?></strong><small>✓ Sudah diberi telaah</small></article>
    <article class="faculty-stat faculty-stat-final"><span>PENETAPAN AKHIR</span><strong>International Office</strong><small>◷ Otoritas kelulusan akhir</small></article>
</section>

<section class="faculty-guidance">
    <span class="faculty-guidance-icon">ⓘ</span>
    <div><strong>Panduan Alur Verifikasi &amp; Saran Akademik Fakultas</strong><small>Pusat pemantauan antrean pendaftar internasional Fakultas Kedokteran UNAND T.A. 2026/2027</small></div>
    <div class="faculty-legend"><span><?= Html::encode('Menunggu: ' . $pendingApplicants) ?></span><span><?= Html::encode('Selesai: ' . $evaluatedApplicants) ?></span></div>
</section>

<section class="faculty-panel" id="pendaftar">
    <div class="faculty-panel-heading">
        <div><span class="faculty-list-icon">▤</span><div><h2>Antrean Pendaftar Fakultas Kedokteran</h2><small>Tahun Akademik 2026/2027 · Program Pendidikan Dokter &amp; Spesialis Terkait</small></div></div>
        <span class="faculty-count"><?= Html::encode((string) count($applicants)) ?> Berkas Pendaftar</span>
    </div>
    <form class="faculty-toolbar" method="get" action="<?= Html::encode(Url::to(['/fakultas/index'])) ?>">
        <label class="faculty-search"><span>⌕</span><input type="search" name="q" value="<?= Html::encode($search) ?>" placeholder="Cari ID, nama, negara, atau paspor..." aria-label="Cari nama, negara, nomor pendaftaran, program studi, atau nomor paspor"></label>
        <button class="faculty-export" type="submit" formaction="<?= Html::encode(Url::to(['/fakultas/export-csv'])) ?>" formmethod="get">⇩ Export CSV</button>
    </form>
    <?php if ($applicants): ?>
        <div class="faculty-table-wrap">
            <table class="faculty-table">
                <thead><tr><th>ID PENDAFTARAN</th><th>PENDAFTAR &amp; KEWARGANEGARAAN</th><th>PROGRAM STUDI</th><th>KUALIFIKASI SAINS</th><th>KEMAHIRAN BAHASA</th><th>STATUS EVALUASI</th><th>AKSI VERIFIKATOR</th></tr></thead>
                <tbody>
                <?php foreach ($applicants as $applicant): ?>
                    <?php $isEvaluated = $applicant['saran_fakultas'] !== ''; ?>
                    <tr>
                        <td><strong>#<?= Html::encode(str_pad((string) $applicant['pendaftaran_id'], 4, '0', STR_PAD_LEFT)) ?></strong><small>Paspor: <?= Html::encode($applicant['no_paspor']) ?></small></td>
                        <td>
                            <div class="faculty-applicant">
                                <span class="faculty-country-avatar"><?= Html::encode(mb_strtoupper(mb_substr($applicant['nama_lengkap'], 0, 1))) ?></span>
                                <div><strong><?= Html::encode($applicant['nama_lengkap']) ?></strong><small><?= Html::encode($applicant['asal_negara']) ?> · <?= Html::encode($applicant['kode_negara']) ?></small></div>
                            </div>
                        </td>
                        <td><strong><?= Html::encode($applicant['program_studi']) ?></strong><small>Fakultas Kedokteran (S-1)</small></td>
                        <td>
                            <strong>♟ Biologi: <?= Html::encode($applicant['biologi']) ?>/10</strong>
                            <small>Kimia: <?= Html::encode($applicant['kimia']) ?>/10</small>
                            <span class="faculty-equivalence"><?= Html::encode('Kesetaraan ' . $applicant['kesetaraan']) ?></span>
                            <?php if ($isEvaluated):
                                ?><span class="faculty-recommendation-result"><?= Html::encode($applicant['saran_fakultas']) ?></span><?php
                            endif; ?>
                        </td>
                        <td><strong><?= Html::encode($applicant['kemahiran_bahasa']) ?></strong><small><?= Html::encode($applicant['status_bahasa']) ?></small></td>
                        <td><span class="<?= $isEvaluated ? 'faculty-status-done' : 'faculty-status-wait' ?>"><?= $isEvaluated ? '✓ Selesai Dievaluasi' : '• Menunggu Evaluasi' ?></span></td>
                        <td>
                            <?= Html::a($isEvaluated ? '◉ Lihat Saran' : '⚑ Mulai Evaluasi', ['/fakultas/evaluasi', 'id' => $applicant['pendaftaran_id']], [
                                'class' => $isEvaluated ? 'faculty-action faculty-action-light' : 'faculty-action faculty-action-primary',
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="faculty-table-foot">
            <span>Menampilkan <?= Html::encode((string) count($applicants)) ?> dari <?= Html::encode((string) $totalApplicants) ?> pendaftar yang dialokasikan ke Fakultas Kedokteran</span>
            <span class="faculty-pagination"><strong>1</strong> Halaman 1 dari 1</span>
        </div>
    <?php else: ?>
        <div class="faculty-empty">Tidak ada pendaftar yang cocok dengan pencarian.</div>
    <?php endif; ?>
</section>

<footer class="faculty-help"><span>ⓘ</span><div><strong>Butuh Konfirmasi Kesetaraan Kurikulum &amp; Silabus Internasional?</strong><small>Hubungi Komisi Kurikulum Fakultas via SIA UNAND ext. 1204 / BAK Pusat</small></div><small>Verifikator: <strong><?= Html::encode($evaluatorName) ?></strong></small></footer>
</div>
