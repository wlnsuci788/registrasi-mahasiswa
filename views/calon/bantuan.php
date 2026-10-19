<?php

use yii\helpers\Html;

/** @var yii\web\View $this */

$this->title = 'Bantuan / Panduan';
?>

<div class="cal-dashboard">
    <div class="cal-greeting">
        <div>
            <div class="eyebrow">PUSAT BANTUAN</div>
            <h1>Bantuan / Panduan</h1>
            <p class="muted">Informasi singkat untuk membantu proses pendaftaran Anda.</p>
        </div>
    </div>
    <section class="card-p">
        <h2 class="help-heading">Cara mengisi formulir</h2>
        <ol class="help-steps">
            <li>Isi data diri sesuai dokumen resmi seperti paspor atau KTP.</li>
            <li>Pilih kategori dan program yang sesuai dengan rencana studi.</li>
            <li>Pilih <b>Simpan Draft</b> untuk melanjutkan pengisian nanti, atau <b>Kirim Pendaftaran</b> jika data sudah benar.</li>
            <li>Pantau perkembangan seleksi melalui menu <b>Status Pendaftaran</b>.</li>
        </ol>
    </section>
    <section class="card-p candidate-help">
        <div class="eyebrow">KONTAK KANTOR URUSAN INTERNASIONAL (KUI)</div>
        <p>Jika ada kendala saat mengisi formulir, hubungi tim bantuan pendaftaran.</p>
        <?= Html::a('io@unand.ac.id', 'mailto:io@unand.ac.id') ?>
        <a href="tel:+6275171181">+62 751 71181</a>
        <span>Hari kerja, pukul 08.00–16.00 WIB</span>
    </section>
</div>
