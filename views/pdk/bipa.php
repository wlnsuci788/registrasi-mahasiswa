<?php

use yii\helpers\Html;

/** @var yii\web\View $this */

$this->title = 'BIPA & Credit Earning';
$bipa = [
    ['A1', 'BIPA Tingkat 1 (Dasar / A1–A2)', 'Semester 1 (Gasal/Genap)', '15 kuota', 'Fokus pada kecakapan situasional, tata bahasa dasar, dan pengenalan kosakata kearifan lokal Minangkabau/Indonesia.'],
    ['B1', 'BIPA Tingkat 2 (Madya / B1–B2)', 'Semester Menengah', '12 kuota', 'Penggunaan akademik dan pemahaman wacana artikel kebudayaan Sumatera Barat, interaksi sosial formal, serta masyarakat adat.'],
    ['C1', 'BIPA Tingkat 3 (Mahir / C1)', 'Semester Lanjutan', '10 kuota', 'Kajian sastra kontemporer, penulisan esai kritis, debat akademik, serta presentasi laporan ilmiah.'],
];
$clusters = [
    ['Fakultas Ilmu Budaya (FIB)', 'Kluster Budaya & Etnografi Minangkabau', 'Total 10 SKS ditawarkan', ['Sastra Lisan Randai', 'Dialektologi Minang', 'Antropologi Maritim']],
    ['FMIPA / Pertanian', 'Kluster Biodiversitas Tropika & Konservasi', 'Total 7 SKS ditawarkan', ['Ekologi Hutan Tropis', 'Flora Malesiana']],
    ['FISIP / FEB', 'Kluster Studi Pembangunan Daerah Berkelanjutan', 'Total 6 SKS ditawarkan', ['Ekonomi Nagari', 'Kebijakan Publik Lokal']],
];
?>
<div class="pdk-page">
    <section class="pdk-heading pdk-card">
        <div>
            <div class="pdk-eyebrow">MANAJEMEN KURIKULUM & BEBAN SKS</div>
            <h1>BIPA & Credit Earning UNAND</h1>
            <p>Katalog program Bahasa Indonesia bagi Penutur Asing (BIPA) dan skema penyetaraan Credit Earning.</p>
        </div>
        <?= Html::a('← Dashboard', ['/pdk/index'], ['class' => 'pdk-button secondary']) ?>
    </section>
    <section class="pdk-stats">
        <article class="pdk-stat"><span>Skema terdaftar</span><strong>6</strong><small>3 tingkat BIPA · 3 kluster Credit Earning</small></article>
        <article class="pdk-stat"><span>Batas beban SKS</span><strong>12–20</strong><small>SKS per mahasiswa per semester</small></article>
        <article class="pdk-stat"><span>Mahasiswa aktif</span><strong><?= Html::encode((string) \app\models\Pendaftaran::find()->where(['jalur' => \app\models\Pendaftaran::JALUR_WNI, 'tujuan_proses' => \app\models\Pendaftaran::TUJUAN_PROSES_PDK, 'status' => [\app\models\Pendaftaran::STATUS_TERKIRIM, \app\models\Pendaftaran::STATUS_LOLOS]])->andWhere(['deleted_at' => null])->count()) ?></strong><small>Periode akademik berjalan</small></article>
        <article class="pdk-stat"><span>Standar konversi</span><strong>ECTS</strong><small>Rujukan konversi nilai internasional</small></article>
    </section>
    <section class="pdk-grid-2">
        <div>
            <h2 style="font-size:16px">Katalog Program BIPA</h2>
            <div class="pdk-page">
                <?php foreach ($bipa as [$code, $title, $semester, $quota, $description]): ?>
                    <article class="pdk-program-card">
                        <div class="pdk-heading"><span class="pdk-badge success"><?= Html::encode($code) ?></span><span class="pdk-badge"><?= Html::encode($quota) ?></span></div>
                        <h3><?= Html::encode($title) ?></h3>
                        <p><?= Html::encode($semester) ?><br><?= Html::encode($description) ?></p>
                        <small class="pdk-muted">Durasi: 1 semester · Pembelajaran terstruktur</small>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
        <div>
            <h2 style="font-size:16px">Skema Credit Earning</h2>
            <div class="pdk-page">
                <?php foreach ($clusters as [$faculty, $title, $total, $courses]): ?>
                    <article class="pdk-program-card">
                        <div class="pdk-eyebrow"><?= Html::encode($faculty) ?></div>
                        <h3><?= Html::encode($title) ?></h3>
                        <span class="pdk-badge success"><?= Html::encode($total) ?></span>
                        <p>Mata kuliah pilihan dapat ditransfer melalui persetujuan fakultas dan verifikasi beban belajar.</p>
                        <div class="pdk-course-list">
                            <?php foreach ($courses as $course): ?>
                                <div class="pdk-course"><strong>3 SKS</strong><?= Html::encode($course) ?></div>
                            <?php endforeach; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <section class="pdk-card">
        <div class="pdk-heading"><div><h2 style="font-size:16px;margin:0">Standar konversi nilai & beban belajar</h2><p>Rujukan penilaian untuk proses evaluasi Credit Earning</p></div><span class="pdk-badge">1 SKS UNAND ≈ 1.5–1.8 ECTS</span></div>
        <div class="pdk-table-wrap" style="margin-top:12px">
            <table class="pdk-table">
                <thead><tr><th>Standar nilai asal</th><th>Rentang skor</th><th>Huruf mutu UNAND</th><th>Bobot nilai</th><th>Status konversi</th></tr></thead>
                <tbody>
                <?php foreach ([['A (Excellent)', '85–100', 'A', '4.00'], ['B+ (Very Good)', '80–84.99', 'A-', '3.75'], ['C (Good)', '75–79.99', 'B+', '3.25'], ['D (Satisfactory)', '70–74.99', 'B', '3.00']] as [$source, $range, $grade, $weight]): ?>
                    <tr><td><?= Html::encode($source) ?></td><td><?= Html::encode($range) ?></td><td><?= Html::encode($grade) ?></td><td><?= Html::encode($weight) ?></td><td><span class="pdk-badge success">Dikonversi penuh</span></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="pdk-muted" style="margin-bottom:0">Nilai di bawah batas minimum perlu ditinjau sebelum rekomendasi transfer kredit diberikan.</p>
    </section>
</div>
