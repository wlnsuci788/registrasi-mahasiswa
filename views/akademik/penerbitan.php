<?php

use app\models\Pendaftaran;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var Pendaftaran|null $pendaftaran */
/** @var Pendaftaran[] $registrations */
/** @var int $total */
/** @var int $pending */
/** @var int $issued */
/** @var string $statusFilter */
/** @var yii\data\Pagination $pagination */

$this->title = 'Penerbitan Nomor Registrasi';
?>
<div class="academic-page">
    <?php if (!$pendaftaran): ?>
        <section class="academic-heading academic-card">
            <div>
                <div class="academic-eyebrow">BIRO PENDIDIKAN &amp; AKADEMIK</div>
                <h1>Penerbitan Registration Number</h1>
                <p>Daftar mahasiswa yang lolos seleksi, status penerbitan nomor, dan jumlah yang masih menunggu.</p>
            </div>
            <?= Html::a('Buku registrasi induk', ['/akademik/buku'], ['class' => 'academic-button secondary']) ?>
        </section>
        <section class="academic-stats academic-issuance-stats" aria-label="Ringkasan penerbitan nomor">
            <?= Html::a('<span>SEMUA PENDAFTAR LOLOS</span><strong>' . Html::encode((string) $total) . '</strong><small>Sudah terbit dan belum terbit</small>', ['/akademik/penerbitan', 'status' => 'all'], ['class' => 'academic-stat academic-stat-link' . ($statusFilter === 'all' ? ' is-selected' : '')]) ?>
            <?= Html::a('<span>MENUNGGU PENERBITAN</span><strong>' . Html::encode((string) $pending) . '</strong><small>Belum memiliki registration number</small>', ['/akademik/penerbitan', 'status' => 'pending'], ['class' => 'academic-stat warning academic-stat-link' . ($statusFilter === 'pending' ? ' is-selected' : '')]) ?>
            <?= Html::a('<span>SUDAH DITERBITKAN</span><strong>' . Html::encode((string) $issued) . '</strong><small>Nomor sudah tersimpan</small>', ['/akademik/penerbitan', 'status' => 'issued'], ['class' => 'academic-stat academic-stat-link' . ($statusFilter === 'issued' ? ' is-selected' : '')]) ?>
        </section>
        <section class="academic-card">
            <div class="academic-queue-heading">
                <div>
                    <strong><?= $statusFilter === 'pending' ? 'Belum diterbitkan' : ($statusFilter === 'issued' ? 'Sudah diterbitkan' : 'Daftar semua mahasiswa') ?></strong>
                    <span>Menampilkan <?= count($registrations) ?> dari <?= Html::encode((string) $pagination->totalCount) ?> mahasiswa</span>
                </div>
            </div>
            <div class="academic-table-wrap">
                <table class="academic-table">
                    <thead><tr><th>No</th><th>Nama mahasiswa</th><th>Program / fakultas</th><th>Institusi asal</th><th>Status penerbitan</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php if (!$registrations): ?>
                        <tr><td colspan="6" class="academic-muted"><?= $statusFilter === 'pending' ? 'Tidak ada mahasiswa yang menunggu penerbitan nomor.' : ($statusFilter === 'issued' ? 'Belum ada nomor yang diterbitkan.' : 'Belum ada pendaftar yang memenuhi syarat penerbitan.') ?></td></tr>
                    <?php else: ?>
                        <?php foreach ($registrations as $index => $item): ?>
                            <?php $issued = $item->status === Pendaftaran::STATUS_REGISTRATION_NUMBER_TERBIT; ?>
                            <tr>
                                <td><?= Html::encode((string) ($pagination->offset + $index + 1)) ?></td>
                                <td><strong><?= Html::encode((string) $item->nama_lengkap) ?></strong><br><small class="academic-muted">ID <?= Html::encode((string) $item->pendaftaran_id) ?></small></td>
                                <td><?= Html::encode(ucwords((string) ($item->jenis_program ?: $item->kategori_program))) ?><br><small class="academic-muted"><?= Html::encode((string) ($item->fakultas->nama_fakultas ?? '—')) ?></small></td>
                                <td><?= Html::encode((string) ($item->asal_institusi ?: '—')) ?></td>
                                <td>
                                    <span class="academic-tag <?= $issued ? 'success' : 'warning' ?>"><?= $issued ? 'Sudah diterbitkan' : 'Belum diterbitkan' ?></span>
                                    <?php if ($issued): ?>
                                        <br><strong class="academic-queue-number"><?= Html::encode((string) $item->registration_number) ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td><?= Html::a($issued ? 'Lihat nomor' : 'Terbitkan nomor', ['/akademik/penerbitan', 'id' => $item->pendaftaran_id], ['class' => 'academic-button']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?= \yii\widgets\LinkPager::widget(['pagination' => $pagination, 'options' => ['class' => 'academic-pagination']]) ?>
        </section>
    <?php else: ?>
        <?php
        $issued = $pendaftaran->status === Pendaftaran::STATUS_REGISTRATION_NUMBER_TERBIT;
        $documents = $pendaftaran->dokumenPendaftars;
        $facultyCode = strtoupper((string) ($pendaftaran->fakultas->kode_fakultas ?? 'UN'));
        $facultyCode = substr((string) preg_replace('/[^A-Z0-9]/', '', $facultyCode) ?: 'UN', 0, 6);
        $unitCode = $pendaftaran->tujuan_proses === Pendaftaran::TUJUAN_PROSES_PDK ? 'PDK' : 'INT';
        $numberPreview = sprintf(
            'REG-%s-%s-%s-%05d',
            date('Y'),
            $facultyCode,
            $unitCode,
            $pendaftaran->pendaftaran_id,
        );
        ?>
        <nav class="academic-breadcrumb" aria-label="Breadcrumb">
            <?= Html::a('Dashboard Biro Akademik', ['/akademik/index']) ?>
            <span>›</span>
            <?= Html::a('Penerbitan Nomor', ['/akademik/penerbitan']) ?>
            <span>›</span>
            <strong><?= Html::encode((string) $pendaftaran->nama_lengkap) ?> (REG-<?= Html::encode((string) $pendaftaran->pendaftaran_id) ?>)</strong>
        </nav>
        <section class="academic-detail-heading">
            <div>
                <h1>Penerbitan Registration Number &amp; Akun SIAKAD</h1>
                <p>Verifikasi berkas, tetapkan nomor induk, dan tandai penerbitan registrasi pendaftar.</p>
            </div>
            <?php if (!$issued): ?>
                <?= Html::a('Ke tombol terbitkan', '#issuance-action', ['class' => 'academic-button']) ?>
            <?php else: ?>
                <span class="academic-tag success">Nomor telah diterbitkan</span>
            <?php endif; ?>
        </section>

        <div class="academic-issuance-layout">
            <aside class="academic-page">
                <section class="academic-card academic-identity-card">
                    <?= Html::img(Url::to('@web/images/avatar-mahasiswa.svg'), [
                        'alt' => 'Ilustrasi mahasiswa',
                        'class' => 'academic-student-avatar',
                    ]) ?>
                    <div class="academic-identity-copy">
                        <strong><?= Html::encode((string) $pendaftaran->nama_lengkap) ?></strong>
                        <span>ID Pendaftaran: REG-<?= Html::encode((string) $pendaftaran->pendaftaran_id) ?></span>
                        <span><?= Html::encode(strtoupper((string) $pendaftaran->jalur)) ?> <?= $pendaftaran->asal_negara ? 'Diaspora (' . Html::encode((string) $pendaftaran->asal_negara) . ')' : '' ?></span>
                    </div>
                    <span class="academic-tag success"><?= $issued ? 'Terbit' : '✓' ?></span>
                    <div class="academic-identity-program">
                        <small>PROGRAM AKADEMIK</small>
                        <strong><?= Html::encode(ucwords((string) ($pendaftaran->jenis_program ?: $pendaftaran->kategori_program))) ?><?= $pendaftaran->target_kredit !== null ? ' (' . Html::encode((string) $pendaftaran->target_kredit) . ' SKS)' : '' ?></strong>
                        <span><?= Html::encode((string) ($pendaftaran->fakultas->nama_fakultas ?? 'Fakultas belum ditentukan')) ?></span>
                    </div>
                    <div class="academic-identity-program">
                        <small>INSTITUSI MITRA ASAL</small>
                        <strong><?= Html::encode((string) ($pendaftaran->asal_institusi ?: 'Belum diisi')) ?></strong>
                        <span><?= Html::encode(trim(implode(' · ', array_filter([
                            $pendaftaran->negara_institusi,
                            $pendaftaran->kota_institusi,
                        ]))) ?: 'Informasi lokasi belum diisi') ?></span>
                    </div>
                </section>

                <section class="academic-card">
                    <div class="academic-section-heading">
                        <h2>Validasi Kelayakan Institusi</h2>
                        <span class="academic-tag success"><?= $issued ? 'Terverifikasi' : 'Lolos seleksi' ?></span>
                    </div>
                    <div class="academic-validation-list">
                        <article class="academic-validation-item">
                            <span class="academic-validation-check">✓</span>
                            <div><strong>Rekomendasi hasil seleksi</strong><small><?= Html::encode((string) ($pendaftaran->catatan_keputusan ?: 'Pendaftar dinyatakan lolos seleksi.')) ?></small></div>
                            <b><?= Html::encode((string) $pendaftaran->status) ?></b>
                        </article>
                        <?php if ($pendaftaran->catatan_fakultas): ?>
                            <article class="academic-validation-item">
                                <span class="academic-validation-check">✓</span>
                                <div><strong>Persetujuan fakultas</strong><small><?= Html::encode((string) $pendaftaran->catatan_fakultas) ?></small></div>
                                <b>TERCATAT</b>
                            </article>
                        <?php endif; ?>
                        <article class="academic-validation-item">
                            <span class="academic-validation-check"><?= count($documents) > 0 ? '✓' : '!' ?></span>
                            <div><strong>Kelengkapan berkas</strong><small><?= count($documents) ?> dokumen terunggah pada pendaftaran ini.</small></div>
                            <b><?= count($documents) > 0 ? 'TERSEDIA' : 'KOSONG' ?></b>
                        </article>
                    </div>
                </section>

                <section class="academic-card">
                    <div class="academic-section-heading">
                        <h2>Berkas &amp; Dokumen Legalitas</h2>
                        <span class="academic-muted"><?= count($documents) ?> berkas</span>
                    </div>
                    <div class="academic-documents">
                        <?php if (!$documents): ?>
                            <div class="academic-doc-row"><span>Belum ada dokumen terlampir.</span></div>
                        <?php else: ?>
                            <?php foreach ($documents as $document): ?>
                                <div class="academic-doc-row">
                                    <span class="academic-doc-icon">▤</span>
                                    <div>
                                        <strong><?= Html::encode((string) ($document->dokumen->nama_dokumen ?? $document->nama_file)) ?></strong>
                                        <small><?= Html::encode((string) $document->nama_file) ?></small>
                                    </div>
                                    <span class="academic-tag success">Tersimpan</span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>
            </aside>

            <section class="academic-card academic-issuance-panel">
                <div class="academic-form-heading">
                    <div>
                        <div class="academic-eyebrow">FORMULIR PENETAPAN OTORITAS</div>
                        <h2>Penetapan Nomor Induk Resmi &amp; Akun SIAKAD</h2>
                    </div>
                    <span class="academic-tag <?= $issued ? 'success' : 'warning' ?>"><?= $issued ? '✓ Diterbitkan' : 'Menunggu terbit' ?></span>
                </div>
                <div class="academic-form-section">
                    <strong>Format Skema Penomoran Otomatis UNAND</strong>
                    <div class="academic-form-value">Jalur <?= Html::encode(strtoupper((string) $pendaftaran->jalur)) ?> / <?= Html::encode(ucwords((string) ($pendaftaran->jenis_program ?: $pendaftaran->kategori_program))) ?></div>
                    <small>Format contoh untuk proses lokal. Akun SIAKAD eksternal tidak dibuat dari portal demo ini.</small>
                </div>
                <div class="academic-form-fields">
                    <div><strong>Kode Fakultas &amp; Jurusan</strong><div class="academic-form-value"><?= Html::encode($facultyCode) ?> · <?= Html::encode((string) ($pendaftaran->jurusan_asal ?: 'Program pendaftar')) ?></div></div>
                    <div><strong>Tahun Akademik &amp; Periode</strong><div class="academic-form-value"><?= Html::encode(date('Y') . '/' . (date('Y') + 1)) ?> Ganjil / Genap</div></div>
                </div>
                <div class="academic-registration-box">
                    <div class="academic-section-heading">
                        <strong>Nomor Registrasi Terkalkulasi (NIM / Registration No.)</strong>
                        <span class="academic-tag success"><?= $issued ? 'Tersimpan' : 'Sesuai skema' ?></span>
                    </div>
                    <div class="academic-registration-number"><?= Html::encode((string) ($pendaftaran->registration_number ?: $numberPreview)) ?></div>
                    <small>Format: <b>REG-[TAHUN]-[KODE FAKULTAS]-[UNIT]-[ID]</b>. Nomor akan dicatat pada portal setelah diterbitkan.</small>
                </div>
                <div class="academic-form-section">
                    <strong>Otomasi Sistem Terintegrasi</strong>
                    <div class="academic-integration-check">
                        <span>✓</span>
                        <div><strong>Pencatatan registrasi pada portal</strong><small>Nomor, status, waktu, dan petugas penerbit disimpan pada data pendaftaran.</small></div>
                    </div>
                    <div class="academic-integration-check is-disabled">
                        <span>—</span>
                        <div><strong>Akun dan sinkronisasi SIAKAD</strong><small>Tidak aktif di mode demo; perlu integrasi layanan resmi untuk menjalankannya.</small></div>
                    </div>
                </div>
                <div class="academic-form-section">
                    <strong>Catatan Resmi Petugas Registrasi (Biro Akademik)</strong>
                    <div class="academic-note">Pastikan data identitas dan hasil seleksi telah diperiksa. Penerbitan pada halaman ini merupakan pencatatan lokal, bukan aktivasi akun eksternal.</div>
                </div>
                <div class="academic-issuer">
                    <span class="academic-issuer-avatar"><?= Html::encode(strtoupper(substr((string) Yii::$app->user->identity->nama, 0, 1))) ?></span>
                    <div><small>PEJABAT PENGESAH PENETAPAN</small><strong><?= Html::encode((string) Yii::$app->user->identity->nama) ?></strong><span>Petugas Biro Pendidikan &amp; Akademik</span></div>
                    <span class="academic-tag success">Petugas aktif</span>
                </div>
                <div class="academic-issuance-actions" id="issuance-action">
                    <?= Html::a('Kembali ke dashboard', ['/akademik/index'], ['class' => 'academic-button secondary']) ?>
                    <?= Html::a('Kembali ke daftar', ['/akademik/penerbitan'], ['class' => 'academic-button danger-light']) ?>
                    <?php if (!$issued): ?>
                        <?= Html::beginForm(['/akademik/terbitkan', 'id' => $pendaftaran->pendaftaran_id], 'post', ['class' => 'academic-issuance-submit']) ?>
                        <label><input type="checkbox" name="confirm" value="1" required> Saya sudah memeriksa data pendaftar.</label>
                        <?= Html::submitButton('Terbitkan nomor sekarang', ['class' => 'academic-button']) ?>
                        <?= Html::endForm() ?>
                    <?php else: ?>
                        <?= Html::a('Cetak Lembar Registrasi', '#registration-preview', ['class' => 'academic-button']) ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <?php if ($issued): ?>
            <section class="academic-document-preview" id="registration-preview">
                <div class="academic-preview-toolbar">
                    <div><span class="academic-eyebrow">PRATINJAU DOKUMEN</span><h2>Lembar Tanda Registrasi Sementara (LTRS) Universitas Andalas</h2></div>
                    <?= Html::a('Cetak lembar', '#', ['class' => 'academic-button secondary', 'onclick' => 'window.print(); return false;']) ?>
                </div>
                <article class="academic-preview-paper">
                    <header class="academic-preview-header">
                        <?= Html::img(Url::to('@web/images/unand-logo.png'), ['alt' => 'Logo Universitas Andalas']) ?>
                        <div><strong>KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</strong><b>UNIVERSITAS ANDALAS · BIRO PENDIDIKAN DAN AKADEMIK</b><small>Gedung Rektorat Kampus Limau Manis, Padang · Dokumen pratinjau data demo</small></div>
                        <span>FORMULIR REGISTRASI<br>Status: <?= Html::encode((string) $pendaftaran->status) ?></span>
                    </header>
                    <h3>SURAT TANDA REGISTRASI SEMENTARA MAHASISWA</h3>
                    <p class="academic-preview-number">Nomor: <strong><?= Html::encode((string) $pendaftaran->registration_number) ?></strong></p>
                    <div class="academic-preview-data">
                        <dl>
                            <div><dt>Nama lengkap mahasiswa</dt><dd><?= Html::encode((string) $pendaftaran->nama_lengkap) ?></dd></div>
                            <div><dt>Registration number</dt><dd><?= Html::encode((string) $pendaftaran->registration_number) ?></dd></div>
                            <div><dt>Status kependudukan</dt><dd><?= Html::encode((string) ($pendaftaran->asal_negara ?: 'Indonesia')) ?></dd></div>
                            <div><dt>Universitas asal</dt><dd><?= Html::encode((string) ($pendaftaran->asal_institusi ?: '—')) ?></dd></div>
                        </dl>
                        <dl>
                            <div><dt>Fakultas penyelenggara</dt><dd><?= Html::encode((string) ($pendaftaran->fakultas->nama_fakultas ?? '—')) ?></dd></div>
                            <div><dt>Program akademik</dt><dd><?= Html::encode(ucwords((string) ($pendaftaran->jenis_program ?: $pendaftaran->kategori_program))) ?></dd></div>
                            <div><dt>Beban studi disetujui</dt><dd><?= $pendaftaran->target_kredit !== null ? Html::encode((string) $pendaftaran->target_kredit . ' SKS') : 'Sesuai program' ?></dd></div>
                            <div><dt>Tanggal penerbitan</dt><dd><?= $pendaftaran->registration_at ? Html::encode(Yii::$app->formatter->asDate($pendaftaran->registration_at)) : '—' ?></dd></div>
                        </dl>
                    </div>
                    <footer class="academic-preview-footer">
                        <span class="academic-preview-seal">UNAND<br>REGISTRASI</span>
                        <p><strong>Pratinjau dokumen</strong><br>Data ini dihasilkan untuk simulasi. Bukan surat resmi, bukti penerimaan, atau kredensial SIAKAD.</p>
                        <div><small>Petugas Biro Akademik</small><strong><?= Html::encode((string) Yii::$app->user->identity->nama) ?></strong></div>
                    </footer>
                </article>
            </section>
        <?php endif; ?>
    <?php endif; ?>
</div>
