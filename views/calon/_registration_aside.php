<?php

use app\models\Pendaftaran;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var Pendaftaran $pendaftaran */
/** @var int $activeStep */

$program = $pendaftaran->jenjang ?: $pendaftaran->jenis_program ?: $pendaftaran->kategori_program;
$quotaExamples = [
    ['Fakultas Kedokteran', '18 / 25 Terisi', 72],
    ['Farmasi', '12 / 20 Terisi', 60],
    ['Teknik & IT', '22 / 40 Terisi', 55],
    ['Ilmu Budaya (FIB)', '8 / 30 Terisi', 27],
];
?>

<aside class="registration-aside <?= $activeStep === 2 ? 'program-aside' : '' ?>">
    <section class="registration-summary card-p">
        <div class="registration-summary-avatar" aria-hidden="true"><?= Html::encode(mb_strtoupper(mb_substr($pendaftaran->nama_lengkap ?: 'P', 0, 1))) ?></div>
        <div>
            <strong><?= Html::encode($pendaftaran->nama_lengkap ?: 'Data pendaftar') ?></strong>
            <span><?= Html::encode($pendaftaran->asal_negara ?: 'Negara asal belum diisi') ?></span>
        </div>
        <dl>
            <dt>ID Pendaftaran</dt>
            <dd><?= $pendaftaran->pendaftaran_id ? Html::encode((string) $pendaftaran->pendaftaran_id) : 'Akan dibuat setelah data disimpan' ?></dd>
            <dt>Jalur</dt>
            <dd><?= Html::encode($pendaftaran->jalur ?: 'Belum dipilih') ?></dd>
            <dt>Program</dt>
            <dd><?= Html::encode($program ?: 'Belum dipilih') ?></dd>
            <?php if ($pendaftaran->fakultas): ?>
                <dt>Fakultas</dt>
                <dd><?= Html::encode($pendaftaran->fakultas->nama_fakultas) ?></dd>
            <?php endif; ?>
        </dl>
    </section>
    <?php if ($activeStep === 2): ?>
        <section class="card-p program-quota-card">
            <div class="program-aside-heading">
                <h2>Kuota Mahasiswa Asing 2026/2027</h2>
                <span>CONTOH</span>
            </div>
            <p class="program-aside-note">Ilustrasi tampilan saja; bukan data kuota resmi.</p>
            <?php foreach ($quotaExamples as [$faculty, $count, $percentage]): ?>
                <div class="program-quota-item">
                    <div><strong><?= Html::encode($faculty) ?></strong><span><?= Html::encode($count) ?></span></div>
                    <div class="program-quota-track" aria-hidden="true"><span style="width: <?= (int) $percentage ?>%"></span></div>
                    <small>Ilustrasi kuota</small>
                </div>
            <?php endforeach; ?>
        </section>
        <section class="card-p program-accreditation-card">
            <h2>Informasi Akreditasi</h2>
            <p>Informasi status akreditasi program studi dapat diperiksa melalui laman resmi fakultas dan Universitas Andalas.</p>
            <a href="https://www.unand.ac.id/" target="_blank" rel="noopener noreferrer">Kunjungi situs resmi UNAND ↗</a>
        </section>
        <section class="registration-campus-card program-campus-card">
            <?= Html::img(Url::to('@web/images/unand-campus-registration.png'), [
                'alt' => 'Kawasan kampus Universitas Andalas Limau Manis, Padang',
            ]) ?>
            <div>
                <span>KAMPUS LIMAU MANIS, PADANG</span>
                <strong>Universitas Andalas</strong>
                <small>Kampus utama di kawasan Limau Manis</small>
            </div>
        </section>
        <section class="card-p program-consultation-card">
            <h2>Konsultasi Pemilihan Program</h2>
            <p>Perlu bantuan menentukan program? Hubungi Kantor Urusan Internasional.</p>
            <a href="mailto:io@unand.ac.id">Email: io@unand.ac.id</a>
        </section>
    <?php elseif ($activeStep === 3): ?>
        <section class="card-p academic-admission-card">
            <div class="academic-aside-heading">
                <span aria-hidden="true">✓</span>
                <div><small>ACUAN KEMAMPUAN BAHASA</small><h2>Standar Tes Bahasa</h2></div>
            </div>
            <p>Contoh skor acuan pada formulir. Persyaratan resmi dapat berbeda menurut fakultas dan program.</p>
            <div class="academic-test-threshold"><span>IELTS Academic</span><strong>Min. Band 6.0</strong></div>
            <div class="academic-test-threshold"><span>TOEFL iBT Official</span><strong>Min. Skor 75</strong></div>
            <div class="academic-test-threshold"><span>Duolingo Test (DET)</span><strong>Min. 105</strong></div>
            <p class="academic-threshold-note">Konfirmasikan persyaratan terbaru kepada fakultas atau KUI.</p>
            <?php if ($pendaftaran->fakultas): ?>
                <div class="academic-faculty-note">
                    <span>Fakultas pilihan</span>
                    <strong><?= Html::encode($pendaftaran->fakultas->nama_fakultas) ?></strong>
                </div>
            <?php endif; ?>
        </section>
        <section class="registration-campus-card academic-campus-card">
            <?= Html::img(Url::to('@web/images/unand-campus-registration.png'), [
                'alt' => 'Kawasan kampus Universitas Andalas Limau Manis, Padang',
            ]) ?>
            <div>
                <span>UNIVERSITAS ANDALAS</span>
                <strong>Kampus Limau Manis</strong>
                <small>Padang, Sumatera Barat</small>
            </div>
        </section>
        <section class="card-p academic-next-step-card">
            <h2>Langkah Persiapan Tahap 4</h2>
            <p>Setelah menyimpan riwayat akademik, siapkan dokumen pendukung pada tahap berikutnya.</p>
            <ul>
                <li>Ijazah / bukti pendidikan terakhir</li>
                <li>Transkrip nilai atau rapor</li>
                <li>Sertifikat bahasa (jika tersedia)</li>
            </ul>
            <span>Berikutnya: Unggah Dokumen</span>
        </section>
        <section class="card-p academic-contact-card">
            <h2>Tim Admisi Internasional (KUI)</h2>
            <p>Perlu bantuan terkait persyaratan akademik?</p>
            <a href="mailto:io@unand.ac.id">io@unand.ac.id</a>
        </section>
    <?php else: ?>
    <section class="card-p registration-help-card">
        <h2><?= $activeStep < 4 ? 'Informasi Pendaftaran' : 'Panduan Tahap Ini' ?></h2>
        <?php if ($activeStep < 4): ?>
            <ul>
                <li>Simpan draft kapan saja dan lanjutkan kembali nanti.</li>
                <li>Pastikan informasi sesuai dengan dokumen resmi.</li>
                <li>Tahapan dapat dibuka kembali sebelum pendaftaran dikirim.</li>
            </ul>
        <?php elseif ($activeStep === 4): ?>
            <ul>
                <li>Unggah dokumen sesuai persyaratan yang ditampilkan.</li>
                <li>Format yang diterima PDF, JPG, dan PNG.</li>
                <li>Ukuran maksimal 5 MB per berkas.</li>
            </ul>
        <?php elseif ($activeStep === 5): ?>
            <p>Warga negara asing dapat mengisi permohonan bantuan visa pada tahap ini. Pendaftar WNI tidak memerlukan visa pelajar.</p>
        <?php else: ?>
            <p>Periksa kembali seluruh data dan dokumen sebelum mengirim pendaftaran untuk diverifikasi petugas.</p>
        <?php endif; ?>
    </section>
    <section class="card-p registration-help-card">
        <h2>Butuh Bantuan?</h2>
        <p>Hubungi Kantor Urusan Internasional jika membutuhkan bantuan pengisian.</p>
        <a href="mailto:io@unand.ac.id">io@unand.ac.id</a>
        <span>Gedung Rektorat Lt. 2, Universitas Andalas</span>
    </section>
    <?php endif; ?>
</aside>
