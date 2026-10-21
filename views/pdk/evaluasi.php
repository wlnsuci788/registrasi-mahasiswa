<?php

use app\models\Pendaftaran;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Pendaftaran $pendaftaran */

$this->title = 'Evaluasi Seleksi Pendaftar WNI';
$documents = $pendaftaran->dokumenPendaftars;
$isPending = $pendaftaran->status === Pendaftaran::STATUS_TERKIRIM;
$needsRevision = $isPending && $pendaftaran->keputusan_at !== null;
?>
<div class="pdk-page">
    <div class="pdk-eyebrow">
        <?= Html::a('Dashboard UPT PDK', ['/pdk/index']) ?> &nbsp;›&nbsp;
        <?= Html::a('Pendidikan Luar Kampus', ['/pdk/pendidikan']) ?> &nbsp;›&nbsp;
        Evaluasi pendaftar
    </div>
    <section class="pdk-heading pdk-card">
        <div><span class="pdk-badge success">Program WNI · Berkas pendaftar</span><h1>Evaluasi Seleksi Pendaftar WNI</h1><p>Periksa data akademik, kelengkapan dokumen, dan simpan rekomendasi seleksi.</p></div>
        <div class="pdk-actions">
            <?= Html::a('Kembali ke daftar', ['/pdk/pendidikan'], ['class' => 'pdk-button secondary']) ?>
            <?= Html::a('Riwayat seleksi', ['/pdk/riwayat'], ['class' => 'pdk-button']) ?>
        </div>
    </section>
    <div class="pdk-evaluation-layout">
        <div class="pdk-page">
            <section class="pdk-card">
                <div class="pdk-profile">
                    <span class="avatar"><?= Html::encode(strtoupper(substr((string) $pendaftaran->nama_lengkap, 0, 1))) ?></span>
                    <div><strong><?= Html::encode((string) $pendaftaran->nama_lengkap) ?></strong><span class="pdk-muted"><?= Html::encode(strtoupper((string) $pendaftaran->jalur)) ?> · Pendaftaran PDK-<?= Html::encode((string) $pendaftaran->pendaftaran_id) ?></span></div>
                    <span class="pdk-badge <?= $isPending ? '' : 'success' ?>"><?= Html::encode((string) $pendaftaran->status) ?></span>
                </div>
                <div class="pdk-detail-grid">
                    <div class="pdk-detail"><small>Asal institusi</small><strong><?= Html::encode((string) ($pendaftaran->asal_institusi ?: 'Belum diisi')) ?></strong></div>
                    <div class="pdk-detail"><small>Program pilihan</small><strong><?= Html::encode(ucwords((string) ($pendaftaran->jenis_program ?: $pendaftaran->kategori_program))) ?></strong></div>
                    <div class="pdk-detail"><small>Fakultas tujuan</small><strong><?= Html::encode((string) ($pendaftaran->fakultas->nama_fakultas ?? 'Belum ditentukan')) ?></strong></div>
                    <div class="pdk-detail"><small>Nilai akhir / IPK</small><strong><?= Html::encode((string) ($pendaftaran->nilai_akhir ?? 'Belum diisi')) ?></strong></div>
                    <div class="pdk-detail"><small>Target beban kredit</small><strong><?= Html::encode((string) ($pendaftaran->target_kredit ?? 'Belum diisi')) ?> SKS</strong></div>
                    <div class="pdk-detail"><small>Tanggal pengajuan</small><strong><?= $pendaftaran->submitted_at ? Html::encode(Yii::$app->formatter->asDatetime($pendaftaran->submitted_at)) : '—' ?></strong></div>
                </div>
            </section>
            <section class="pdk-card">
                <div class="pdk-heading"><div><h2 style="font-size:16px;margin:0">Dokumen persyaratan</h2><p><?= count($documents) ?> berkas terunggah</p></div><span class="pdk-badge <?= count($documents) ? 'success' : 'danger' ?>"><?= count($documents) ? 'Berkas tersedia' : 'Belum ada berkas' ?></span></div>
                <div class="pdk-doc-list" style="margin-top:12px">
                    <?php if (!$documents): ?>
                        <div class="pdk-doc-row"><span>Belum ada dokumen yang diunggah untuk pendaftaran ini.</span></div>
                    <?php else: ?>
                        <?php foreach ($documents as $document): ?>
                            <div class="pdk-doc-row">
                                <div><strong><?= Html::encode((string) ($document->dokumen->nama_dokumen ?? $document->nama_file)) ?></strong><br><small class="pdk-muted"><?= Html::encode((string) $document->nama_file) ?></small></div>
                                <span class="pdk-badge success">Terunggah</span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
            <section class="pdk-card">
                <h2 style="font-size:16px;margin-top:0">Ringkasan akademik</h2>
                <div class="pdk-detail-grid">
                    <div class="pdk-detail"><small>Jurusan asal</small><strong><?= Html::encode((string) ($pendaftaran->jurusan_asal ?: 'Belum diisi')) ?></strong></div>
                    <div class="pdk-detail"><small>Negara / kota institusi</small><strong><?= Html::encode(implode(', ', array_filter([$pendaftaran->negara_institusi, $pendaftaran->kota_institusi]))) ?: 'Belum diisi' ?></strong></div>
                    <div class="pdk-detail"><small>Skala nilai</small><strong><?= Html::encode((string) ($pendaftaran->skala_nilai ?: 'Belum diisi')) ?></strong></div>
                    <div class="pdk-detail"><small>Status mahasiswa asal</small><strong><?= Html::encode((string) ($pendaftaran->status_mahasiswa_asal ?: 'Belum diisi')) ?></strong></div>
                </div>
            </section>
        </div>
        <aside class="pdk-page">
            <section class="pdk-card">
                <div class="pdk-eyebrow">REKOMENDASI UPT PDK</div>
                <?php if ($isPending): ?>
                    <?= Html::beginForm(['/pdk/evaluasi', 'id' => $pendaftaran->pendaftaran_id], 'post', ['class' => 'pdk-form', 'style' => 'margin-top:12px']) ?>
                    <label for="keputusan">Hasil seleksi administratif</label>
                    <select id="keputusan" name="keputusan" class="pdk-form-control" required>
                        <option value="">Pilih rekomendasi...</option>
                        <option value="lolos">Direkomendasikan lolos</option>
                        <option value="revisi" <?= $needsRevision ? 'selected' : '' ?>>Perlu revisi dokumen</option>
                        <option value="tidak-lolos">Tidak direkomendasikan lolos</option>
                    </select>
                    <label for="catatan-keputusan">Catatan evaluator</label>
                    <textarea id="catatan-keputusan" name="catatan_keputusan" class="pdk-form-control" placeholder="Tuliskan alasan atau catatan untuk pendaftar..." required><?= Html::encode((string) $pendaftaran->catatan_keputusan) ?></textarea>
                    <small class="pdk-muted">Catatan wajib disertakan untuk setiap keputusan seleksi.</small>
                    <?= Html::submitButton('Simpan rekomendasi', ['class' => 'pdk-button']) ?>
                    <?= Html::endForm() ?>
                <?php else: ?>
                    <div class="pdk-alert" style="margin-top:12px">Keputusan seleksi telah tersimpan.<br><strong><?= Html::encode((string) $pendaftaran->status) ?></strong></div>
                    <div class="pdk-detail" style="margin-top:10px"><small>Catatan evaluator</small><strong><?= Html::encode((string) ($pendaftaran->catatan_keputusan ?: 'Tidak ada catatan')) ?></strong></div>
                    <?php if ($pendaftaran->keputusan_at): ?>
                        <p class="pdk-muted">Disimpan <?= Html::encode(Yii::$app->formatter->asDatetime($pendaftaran->keputusan_at)) ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            </section>
            <section class="pdk-card">
                <div class="pdk-eyebrow">PANDUAN VERIFIKASI</div>
                <p class="pdk-subtitle">Pastikan berkas sesuai dengan identitas, program yang dipilih, serta ketentuan SKS. Jika berkas perlu dilengkapi, pilih revisi dan jelaskan kekurangannya pada catatan.</p>
            </section>
        </aside>
    </div>
</div>
