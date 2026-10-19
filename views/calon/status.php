<?php

use app\models\Pendaftaran;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Pendaftaran|null $pendaftaran */

$this->title = 'Status Pendaftaran';
$statusClass = match ($pendaftaran?->status) {
    Pendaftaran::STATUS_LOLOS, Pendaftaran::STATUS_REGISTRATION_NUMBER_TERBIT => 'is-success',
    Pendaftaran::STATUS_TIDAK_LOLOS => 'is-danger',
    Pendaftaran::STATUS_TERKIRIM => 'is-progress',
    default => 'is-muted',
};
?>

<div class="cal-dashboard">
    <div class="cal-greeting">
        <div>
            <div class="eyebrow">PEMANTAUAN PENDAFTARAN</div>
            <h1>Status Pendaftaran</h1>
            <p class="muted">Periksa status terbaru dan informasi pendaftaran Anda.</p>
        </div>
        <?php if ($pendaftaran): ?>
            <span class="status-pill <?= $statusClass ?>">
                <?= Html::encode($pendaftaran->status ?: 'Status belum ditentukan') ?>
            </span>
        <?php endif; ?>
    </div>

    <?php if (!$pendaftaran): ?>
        <section class="card-p candidate-empty">
            <div class="program-icon" aria-hidden="true">&#128221;</div>
            <div>
                <strong>Belum ada pendaftaran</strong>
                <p class="muted">Mulai isi formulir untuk melihat status pendaftaran Anda di halaman ini.</p>
                <?= Html::a('Buka Formulir Registrasi', ['form'], ['class' => 'btn-primary']) ?>
            </div>
        </section>
    <?php else: ?>
        <section class="card-p candidate-card">
            <div class="candidate-details">
                <div><span>Nama pendaftar</span><strong><?= Html::encode($pendaftaran->nama_lengkap ?: 'Belum diisi') ?></strong></div>
                <div><span>Nomor pendaftaran</span><strong><?= Html::encode((string) $pendaftaran->pendaftaran_id) ?></strong></div>
                <div><span>Registration Number</span><strong><?= Html::encode($pendaftaran->registration_number ?: 'Belum diterbitkan') ?></strong></div>
                <div><span>Tanggal dikirim</span><strong><?= Html::encode($pendaftaran->submitted_at ?: 'Belum dikirim') ?></strong></div>
            </div>
        </section>
        <section class="card-p candidate-progress">
            <div class="eyebrow">CATATAN PENDAFTARAN</div>
            <h2>Status seleksi</h2>
            <p class="muted"><?= Html::encode($pendaftaran->catatan_keputusan ?: 'Belum ada catatan keputusan dari petugas.') ?></p>
            <?php if ($pendaftaran->status === Pendaftaran::STATUS_DRAFT): ?>
                <?= Html::a('Lanjutkan Pengisian Formulir', ['form'], ['class' => 'btn-primary']) ?>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</div>
