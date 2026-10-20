<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array[] $visaList */

$this->title = 'Bantuan Visa International Office';
?>
<?= $this->render('_demo_notice') ?>
<div class="io-breadcrumb">INTERNATIONAL OFFICE <span>›</span> LAYANAN IMIGRASI <span>›</span> BANTUAN VISA</div>
<section class="io-page-heading">
    <div>
        <h1>Pengelolaan &amp; Layanan Bantuan Visa</h1>
        <p class="muted">Pantau dan perbarui status fasilitas bantuan visa mahasiswa asing Universitas Andalas.</p>
    </div>
    <div class="io-heading-badge"><small>TOTAL PERMOHONAN</small><strong><?= Html::encode((string) count($visaList)) ?></strong></div>
</section>
<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="io-alert io-alert-success"><?= Html::encode(Yii::$app->session->getFlash('success')) ?></div>
<?php endif; ?>

<?php if ($visaList): ?>
    <div class="io-visa-grid">
        <section class="io-panel">
            <div class="io-panel-heading"><div><h2>Antrean Pengajuan Bantuan Visa</h2><p class="muted">Pengajuan visa yang membutuhkan tindak lanjut.</p></div></div>
            <div class="io-visa-list">
                <?php foreach ($visaList as $visa): ?>
                    <a class="io-visa-list-item" href="#visa-<?= Html::encode((string) $visa['visa_id']) ?>">
                        <strong>#<?= Html::encode(str_pad((string) $visa['pendaftaran_id'], 4, '0', STR_PAD_LEFT)) ?> · Nguyen Van An</strong>
                        <small>Vietnam · Visa Pelajar (C316)</small>
                        <span class="io-status"><?= Html::encode($visa['status']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="io-help-note">
                <strong>Informasi proses visa</strong>
                <p>Pastikan dokumen paspor dan rincian kedatangan lengkap sebelum meneruskan proses rekomendasi visa.</p>
            </div>
        </section>

        <div class="io-visa-details">
            <?php foreach ($visaList as $visa): ?>
                <section class="io-panel io-visa-card" id="visa-<?= Html::encode((string) $visa['visa_id']) ?>">
                    <div class="io-panel-heading">
                        <div><h2>Detail Bantuan Visa</h2><p class="muted">Permohonan #<?= Html::encode((string) $visa['visa_id']) ?></p></div>
                        <span class="io-status"><?= Html::encode($visa['status']) ?></span>
                    </div>
                    <div class="io-visa-profile">
                        <span class="io-avatar">NV</span>
                        <div><strong>Nguyen Van An</strong><small>Vietnam · S1 Pendidikan Dokter</small></div>
                    </div>
                    <dl class="io-data-grid io-visa-data">
                        <div><dt>Nomor paspor</dt><dd>B9823411</dd></div>
                        <div><dt>Masa berlaku paspor</dt><dd><?= Html::encode(Yii::$app->formatter->asDate($visa['masa_berlaku_paspor'], 'php:d M Y')) ?></dd></div>
                        <div><dt>Jenis visa</dt><dd>Visa Pelajar (C316)</dd></div>
                        <div><dt>Tanggal kedatangan</dt><dd><?= Html::encode(Yii::$app->formatter->asDate($visa['tanggal_kedatangan'], 'php:d M Y')) ?></dd></div>
                        <div><dt>Tujuan</dt><dd><?= Html::encode($visa['tujuan']) ?></dd></div>
                        <div><dt>Kota kedatangan</dt><dd><?= Html::encode($visa['kota_kedatangan']) ?></dd></div>
                    </dl>
                    <form method="post" class="io-visa-form">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                        <?= Html::hiddenInput('visa_id', (string) $visa['visa_id']) ?>
                        <label class="io-field-label" for="visa-status-<?= Html::encode((string) $visa['visa_id']) ?>">Perbarui status</label>
                        <select class="io-select" id="visa-status-<?= Html::encode((string) $visa['visa_id']) ?>" name="status" required>
                            <?php foreach (['Menunggu diproses', 'Diproses', 'Perlu data tambahan', 'Selesai'] as $value): ?>
                                <option value="<?= Html::encode($value) ?>" <?= $visa['status'] === $value ? 'selected' : '' ?>><?= Html::encode($value) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label class="io-field-label" for="visa-note-<?= Html::encode((string) $visa['visa_id']) ?>">Catatan petugas</label>
                        <textarea class="io-textarea" id="visa-note-<?= Html::encode((string) $visa['visa_id']) ?>" name="catatan" rows="3" maxlength="2000" placeholder="Catatan akan ditampilkan pada status pendaftar..."><?= Html::encode($visa['catatan']) ?></textarea>
                        <button class="io-button io-button-primary" type="submit">Simpan status visa</button>
                    </form>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
<?php else: ?>
    <section class="io-panel io-empty">Belum ada permohonan bantuan visa dari mahasiswa asing.</section>
<?php endif; ?>
