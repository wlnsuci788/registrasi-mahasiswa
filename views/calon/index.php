<?php

use app\models\Pendaftaran;
use app\models\User;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Pendaftaran|null $pendaftaran */
/** @var User $user */
/** @var int $completedSteps */

$this->title = 'Beranda';
$user = Yii::$app->user->identity;
if (!$user instanceof User) {
    throw new \UnexpectedValueException('The authenticated identity is not a user account.');
}
$nama = $pendaftaran && $pendaftaran->nama_lengkap
    ? $pendaftaran->nama_lengkap
    : $user->nama;
$status = $pendaftaran
    ? ($pendaftaran->status ?: 'Status belum ditentukan')
    : 'Belum mendaftar';
$statusClass = match ($status) {
    Pendaftaran::STATUS_LOLOS, Pendaftaran::STATUS_REGISTRATION_NUMBER_TERBIT => 'is-success',
    Pendaftaran::STATUS_TIDAK_LOLOS => 'is-danger',
    Pendaftaran::STATUS_TERKIRIM => 'is-progress',
    default => 'is-muted',
};
$fakultas = $pendaftaran && $pendaftaran->fakultas
    ? $pendaftaran->fakultas->nama_fakultas
    : null;
?>

<div class="cal-dashboard">
    <div class="cal-greeting">
        <div>
            <div class="eyebrow">PORTAL PENERIMAAN MAHASISWA ASING</div>
            <h1>Halo, <?= Html::encode($nama) ?></h1>
            <p class="muted">Pantau informasi dan proses pendaftaran Anda di Universitas Andalas.</p>
        </div>
        <span class="academic-year">TA 2026/2027</span>
    </div>

    <div class="candidate-grid">
        <section class="card-p candidate-card">
            <div class="candidate-card-heading">
                <div>
                    <div class="eyebrow">STATUS PENDAFTARAN</div>
                    <h2><?= Html::encode($nama) ?></h2>
                </div>
                <span class="status-pill <?= $statusClass ?>"><?= Html::encode($status) ?></span>
            </div>

            <?php if ($pendaftaran): ?>
                <div class="candidate-program">
                    <div class="program-icon" aria-hidden="true">&#127891;</div>
                    <div>
                        <span class="muted">Pilihan program studi</span>
                        <strong>
                            <?= Html::encode(trim(implode(' ', array_filter([
                                $pendaftaran->jenjang,
                                $pendaftaran->jenis_program ?: $pendaftaran->kategori_program,
                            ]))) ?: 'Belum dipilih') ?>
                        </strong>
                        <span><?= Html::encode($fakultas ?: 'Fakultas belum dipilih') ?></span>
                    </div>
                </div>
                <div class="candidate-details">
                    <div>
                        <span>Registration Number</span>
                        <strong><?= Html::encode($pendaftaran->registration_number ?: 'Belum diterbitkan') ?></strong>
                    </div>
                    <div>
                        <span>Nomor pendaftaran</span>
                        <strong><?= Html::encode((string) $pendaftaran->pendaftaran_id) ?></strong>
                    </div>
                </div>
            <?php else: ?>
                <div class="candidate-empty">
                    <div class="program-icon" aria-hidden="true">&#128221;</div>
                    <div>
                        <strong>Anda belum memiliki pendaftaran</strong>
                        <p class="muted">Informasi program studi dan status pendaftaran akan muncul di sini setelah pendaftaran dibuat.</p>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <aside class="candidate-side">
            <section class="card-p">
                <div class="eyebrow">AKSES CEPAT</div>
                <h2>Informasi Pendaftaran</h2>
                <ul class="candidate-links">
                    <li><span aria-hidden="true">&#10003;</span> <?= Html::a('Lengkapi data pendaftaran', ['form']) ?></li>
                    <li><span aria-hidden="true">&#10003;</span> <?= Html::a('Pantau status seleksi', ['status']) ?></li>
                    <li><span aria-hidden="true">&#10003;</span> Registration Number tersedia setelah diterbitkan</li>
                </ul>
            </section>
            <section class="card-p candidate-help">
                <div class="eyebrow">BUTUH BANTUAN?</div>
                <p>Hubungi Kantor Urusan Internasional (KUI) jika Anda membutuhkan bantuan pendaftaran.</p>
                <?= Html::a('Lihat panduan bantuan', ['bantuan']) ?>
                <?= Html::a('io@unand.ac.id', 'mailto:io@unand.ac.id') ?>
                <span>+62 751 71181</span>
            </section>
        </aside>
    </div>

    <section class="candidate-progress card-p">
        <div class="candidate-card-heading">
            <div>
                <div class="eyebrow">PERJALANAN PENDAFTARAN</div>
                <h2>Tahapan Pendaftaran</h2>
            </div>
            <span class="muted"><?= $completedSteps === 6 ? 'Pendaftaran sudah dikirim' : ($completedSteps ? 'Draft tersimpan' : 'Belum dimulai') ?></span>
        </div>
        <?php
        $steps = [
            ['Data diri', 'form'],
            ['Pilihan program', 'program'],
            ['Data akademik', 'akademik'],
            ['Unggah dokumen', 'dokumen'],
            ['Bantuan visa', 'visa'],
            ['Ringkasan & kirim', 'ringkasan'],
        ];
        ?>
        <ol>
            <?php foreach ($steps as $index => [$tahap, $route]): ?>
                <?php
                $done = $index < $completedSteps;
                $current = $index === $completedSteps;
                $stepRoute = $pendaftaran && $completedSteps === 6 ? ['status'] : [$route];
                ?>
                <li class="<?= $done ? 'is-done' : '' ?> <?= $current ? 'is-current' : '' ?>">
                    <?= Html::a(
                        Html::tag('span', $done ? '&#10003;' : (string) ($index + 1), ['class' => 'step-number'])
                        . Html::tag('span', Html::encode($tahap)),
                        $stepRoute,
                        ['aria-label' => $tahap . ($done ? ', selesai' : ($current ? ', tahap saat ini' : ''))],
                    ) ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </section>
</div>
