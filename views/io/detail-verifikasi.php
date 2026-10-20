<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $pendaftaran */
/** @var string|null $error */

$this->title = 'Detail Verifikasi Pendaftar Internasional';
$this->registerMetaTag(['name' => 'robots', 'content' => 'noindex, nofollow']);
?>
<?= $this->render('_demo_notice') ?>
<div class="io-breadcrumb">
    <?= Html::a('INTERNATIONAL OFFICE', ['/io/index']) ?> <span>›</span>
    <?= Html::a('VERIFIKASI PENDAFTARAN', ['/io/verifikasi']) ?> <span>›</span>
    ID PENDAFTARAN: <?= Html::encode(str_pad((string) $pendaftaran['pendaftaran_id'], 4, '0', STR_PAD_LEFT)) ?>
</div>
<section class="io-page-heading">
    <div>
        <h1>Detail Verifikasi Pendaftar Internasional</h1>
        <p class="muted">Periksa informasi pendaftar, dokumen, dan tetapkan hasil verifikasi.</p>
    </div>
    <?= Html::a('← Daftar pendaftar', ['/io/verifikasi'], ['class' => 'io-button io-button-light']) ?>
</section>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="io-alert io-alert-success"><?= Html::encode(Yii::$app->session->getFlash('success')) ?></div>
<?php endif; ?>

<div class="io-detail-grid">
    <main>
        <section class="io-panel io-applicant-banner">
            <div class="io-avatar"><?= Html::encode(mb_strtoupper(mb_substr($pendaftaran['nama_lengkap'], 0, 2))) ?></div>
            <div class="io-applicant-title">
                <h2><?= Html::encode($pendaftaran['nama_lengkap']) ?></h2>
                <span class="io-tag"><?= Html::encode($pendaftaran['asal_negara']) ?></span>
                <p><?= Html::encode($pendaftaran['jenjang'] ?: $pendaftaran['jenis_program']) ?>
                    · <?= Html::encode($pendaftaran['fakultas']) ?></p>
            </div>
            <span class="io-status"><?= Html::encode($pendaftaran['status']) ?></span>
        </section>

        <section class="io-panel">
            <div class="io-panel-heading">
                <div><h2>Data Pendaftar Internasional</h2><p class="muted">Informasi sesuai formulir pendaftaran.</p></div>
                <span class="io-tag">TA 2026/2027</span>
            </div>
            <dl class="io-data-grid">
                <div><dt>Nama sesuai paspor</dt><dd><?= Html::encode($pendaftaran['nama_lengkap']) ?></dd></div>
                <div><dt>Kewarganegaraan</dt><dd><?= Html::encode($pendaftaran['asal_negara']) ?></dd></div>
                <div><dt>Tempat &amp; tanggal lahir</dt><dd><?= Html::encode($pendaftaran['tempat_lahir'] . ', ' . Yii::$app->formatter->asDate($pendaftaran['tanggal_lahir'], 'php:d F Y')) ?></dd></div>
                <div><dt>Nomor paspor</dt><dd><?= Html::encode($pendaftaran['no_paspor']) ?></dd></div>
                <div><dt>Program studi</dt><dd><?= Html::encode($pendaftaran['jenjang'] ?: $pendaftaran['jenis_program']) ?></dd></div>
                <div><dt>Fakultas</dt><dd><?= Html::encode($pendaftaran['fakultas']) ?></dd></div>
                <div><dt>Kategori program</dt><dd><?= Html::encode(ucfirst($pendaftaran['kategori_program'])) ?></dd></div>
                <div><dt>Institusi asal</dt><dd><?= Html::encode($pendaftaran['asal_institusi']) ?></dd></div>
                <div><dt>Jenis visa</dt><dd><?= Html::encode($pendaftaran['jenis_visa']) ?></dd></div>
                <div><dt>Tanggal pendaftaran</dt><dd><?= Html::encode(Yii::$app->formatter->asDate($pendaftaran['submitted_at'], 'php:d F Y')) ?></dd></div>
            </dl>
        </section>

        <section class="io-panel">
            <div class="io-panel-heading">
                <div><h2>Dokumen Pendaftar</h2><p class="muted"><?= count($pendaftaran['dokumen']) ?> berkas terunggah</p></div>
            </div>
            <?php if ($pendaftaran['dokumen']): ?>
                <div class="io-document-list">
                    <?php foreach ($pendaftaran['dokumen'] as $uploaded): ?>
                        <div class="io-document-row">
                            <span class="io-document-icon">▧</span>
                            <div>
                                <strong><?= Html::encode($uploaded['nama_dokumen']) ?></strong>
                                <small><?= Html::encode($uploaded['nama_file']) ?></small>
                            </div>
                            <span class="io-status io-status-muted">Contoh berkas</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="io-empty">Belum ada dokumen yang diunggah.</div>
            <?php endif; ?>
        </section>
    </main>

    <aside>
        <section class="io-panel io-decision-panel">
            <div class="io-panel-heading"><div><h2>Panel Keputusan Verifikasi</h2><p class="muted">Catatan wajib untuk setiap keputusan.</p></div></div>
            <?php if ($error): ?>
                <div class="io-alert io-alert-error"><?= Html::encode($error) ?></div>
            <?php endif; ?>
            <?php if ($pendaftaran['catatan_keputusan']): ?>
                <div class="io-note">
                    <strong>Keputusan sebelumnya: <?= Html::encode($pendaftaran['status']) ?></strong>
                    <p><?= nl2br(Html::encode($pendaftaran['catatan_keputusan'])) ?></p>
                </div>
            <?php endif; ?>
            <form method="post">
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                <label class="io-field-label" for="io-catatan">Kolom catatan <span>(wajib diisi)</span></label>
                <textarea id="io-catatan" class="io-textarea" name="catatan_keputusan" rows="5" maxlength="2000" required><?= Html::encode($pendaftaran['catatan_keputusan']) ?></textarea>
                <div class="io-decision-buttons">
                    <button class="io-button io-button-primary" type="submit" name="keputusan" value="Lolos">✓ Lolos</button>
                    <button class="io-button io-button-danger" type="submit" name="keputusan" value="Tidak lolos">Tidak lolos</button>
                </div>
            </form>
        </section>
    </aside>
</div>
