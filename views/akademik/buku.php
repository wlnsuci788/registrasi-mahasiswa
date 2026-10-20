<?php

use app\models\Pendaftaran;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Pendaftaran[] $registrations */
/** @var int $total */
/** @var int $regular */
/** @var int $creditEarning */
/** @var int $bipa */
/** @var string $search */
/** @var string $program */

$this->title = 'Buku Registrasi Induk';
?>
<div class="academic-page">
    <section class="academic-heading">
        <div><div class="academic-eyebrow">BUKU REGISTRASI INDUK · DATA REGISTRASI TERVERIFIKASI</div><h1>Buku Registrasi Induk Mahasiswa Internasional &amp; PDK</h1><p>Daftar pendaftar yang telah menerima nomor registrasi dari Biro Akademik.</p></div>
        <div class="academic-actions">
            <?= Html::a('Cetak buku induk', '#', ['class' => 'academic-button secondary', 'onclick' => 'window.print(); return false;']) ?>
            <?= Html::a('Riwayat penerbitan', ['/akademik/riwayat'], ['class' => 'academic-button']) ?>
        </div>
    </section>
    <section class="academic-stats">
        <article class="academic-stat"><span>Total teregistrasi</span><strong><?= Html::encode((string) $total) ?></strong><small>Nomor resmi telah diterbitkan</small></article>
        <article class="academic-stat"><span>Program degree</span><strong><?= Html::encode((string) $regular) ?></strong><small>Mahasiswa program bergelar</small></article>
        <article class="academic-stat"><span>Credit Earning</span><strong><?= Html::encode((string) $creditEarning) ?></strong><small>Mahasiswa program pertukaran kredit</small></article>
        <article class="academic-stat warning"><span>Program BIPA</span><strong><?= Html::encode((string) $bipa) ?></strong><small>Mahasiswa program bahasa</small></article>
    </section>
    <section class="academic-card">
        <?= Html::beginForm(['/akademik/buku'], 'get', ['class' => 'academic-filter']) ?>
        <?= Html::textInput('q', $search, ['placeholder' => 'Cari nomor registrasi, nama, atau institusi...', 'aria-label' => 'Cari buku registrasi']) ?>
        <?= Html::dropDownList('program', $program, ['' => 'Semua program', 'degree' => 'Degree', 'non-degree' => 'Non-degree'], ['aria-label' => 'Filter kategori program']) ?>
        <?= Html::submitButton('Terapkan filter', ['class' => 'academic-button']) ?>
        <?= Html::a('Reset', ['/akademik/buku'], ['class' => 'academic-button secondary']) ?>
        <?= Html::endForm() ?>
        <p class="academic-subtitle" style="margin:10px 0">Periode aktif: TA <?= Html::encode(date('Y') . '/' . (date('Y') + 1)) ?> · Data nomor yang diterbitkan</p>
    </section>
    <section class="academic-card">
        <div class="academic-table-wrap">
            <table class="academic-table">
                <thead><tr><th>No</th><th>Registration number</th><th>Nama mahasiswa</th><th>Negara / institusi asal</th><th>Fakultas &amp; program</th><th>SKS</th><th>Tanggal terbit</th></tr></thead>
                <tbody>
                <?php if (!$registrations): ?>
                    <tr><td colspan="7" class="academic-muted">Belum ada nomor registrasi yang cocok dengan filter.</td></tr>
                <?php else: ?>
                    <?php foreach ($registrations as $index => $item): ?>
                        <tr>
                            <td><?= Html::encode((string) ($index + 1)) ?></td>
                            <td><strong class="academic-tag success"><?= Html::encode((string) $item->registration_number) ?></strong></td>
                            <td><?= Html::a(Html::encode((string) $item->nama_lengkap), ['/akademik/penerbitan', 'id' => $item->pendaftaran_id]) ?><br><small class="academic-muted">ID <?= Html::encode((string) $item->pendaftaran_id) ?></small></td>
                            <td><?= Html::encode((string) ($item->asal_negara ?: 'Indonesia')) ?><br><small class="academic-muted"><?= Html::encode((string) ($item->asal_institusi ?: '—')) ?></small></td>
                            <td><?= Html::encode((string) ($item->fakultas->nama_fakultas ?? '—')) ?><br><small class="academic-muted"><?= Html::encode(ucwords((string) ($item->jenis_program ?: $item->kategori_program))) ?></small></td>
                            <td><?= Html::encode((string) ($item->target_kredit ?? '—')) ?></td>
                            <td><?= $item->registration_at ? Html::encode(Yii::$app->formatter->asDate($item->registration_at)) : '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p class="academic-subtitle" style="margin:10px 0 0">Menampilkan <?= count($registrations) ?> data terbaru · Maksimal 10 data pada daftar ini</p>
    </section>
    <section class="academic-banner"><strong>Klausul sinkronisasi master akademik</strong><p>Daftar ini memuat nomor registrasi yang tersimpan pada portal. Integrasi dengan PDDikti atau SIAKAD harus dilakukan melalui layanan resmi dan tidak berjalan pada mode demo ini.</p></section>
</div>
