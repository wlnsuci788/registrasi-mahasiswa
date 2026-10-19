<?php

use app\models\Pendaftaran;
use yii\helpers\Html;

/** @var Pendaftaran $pendaftaran */
/** @var int $activeStep */

$program = $pendaftaran->jenjang ?: $pendaftaran->jenis_program ?: $pendaftaran->kategori_program;
?>

<aside class="registration-aside">
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
</aside>
