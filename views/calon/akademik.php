<?php

use app\models\Pendaftaran;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Pendaftaran $pendaftaran */
/** @var int $activeStep */
/** @var bool $readOnly */

$this->title = 'Riwayat Akademik';
$degreeProgram = $pendaftaran->kategori_program === Pendaftaran::KATEGORI_PROGRAM_DEGREE;
$creditEarning = $pendaftaran->jenis_program === Pendaftaran::JENIS_PROGRAM_CREDIT_EARNING;
?>

<div class="cal-dashboard registration-dashboard">
    <?= $this->render('_steps', ['activeStep' => $activeStep, 'readOnly' => $readOnly]) ?>
    <div class="registration-grid">
        <div class="registration-main academic-main">
            <div class="registration-intro card-p academic-intro">
                <div class="registration-seal" aria-hidden="true">&#10003;</div>
                <div>
                    <div class="eyebrow">BAGIAN REGISTRASI AKADEMIK</div>
                    <h1>Riwayat Akademik &amp; Kualifikasi Internasional</h1>
                    <p class="muted">Pastikan data riwayat pendidikan Anda sesuai dengan transkrip nilai resmi dan sertifikat kemampuan bahasa yang sah untuk proses seleksi dan administrasi Kementerian Pendidikan RI.</p>
                </div>
                <?php if ($degreeProgram && $pendaftaran->fakultas): ?>
                    <span class="academic-program-badge">
                        <small>Program Terpilih</small>
                        <strong><?= Html::encode($pendaftaran->jenjang . ' · ' . $pendaftaran->fakultas->nama_fakultas) ?></strong>
                    </span>
                <?php endif; ?>
            </div>

            <?= Html::beginForm(['akademik'], 'post', ['class' => 'candidate-form academic-form']) ?>
            <?= $this->render('_readonly_notice', ['readOnly' => $readOnly]) ?>
            <?= Html::errorSummary($pendaftaran, ['class' => 'card-p form-errors']) ?>
            <fieldset class="registration-readonly-fields" <?= $readOnly ? 'disabled' : '' ?>>
                <section class="card-p academic-section">
                    <div class="academic-section-heading">
                        <span class="academic-section-icon" aria-hidden="true">A</span>
                        <div>
                            <h2>Pendidikan Terakhir / Institusi Asal</h2>
                            <p>Detail jenjang, sekolah/universitas, atau instansi pendidikan terakhir Anda.</p>
                        </div>
                        <span class="academic-section-status">Wajib Diisi</span>
                    </div>
                    <div class="form-grid">
                        <div class="candidate-field form-field-wide academic-field-with-note">
                            <?= Html::activeLabel($pendaftaran, 'asal_institusi', ['class' => 'form-label']) ?>
                            <small>Contoh: Hanoi Amsterdam High School</small>
                            <?= Html::activeTextInput($pendaftaran, 'asal_institusi', ['placeholder' => 'Nama sekolah / universitas asal']) ?>
                            <?= Html::error($pendaftaran, 'asal_institusi') ?>
                        </div>
                        <div class="candidate-field">
                            <?= Html::activeLabel($pendaftaran, 'negara_institusi', ['class' => 'form-label']) ?>
                            <?= Html::activeTextInput($pendaftaran, 'negara_institusi', ['placeholder' => 'Pilih / tulis negara asal']) ?>
                            <?= Html::error($pendaftaran, 'negara_institusi') ?>
                        </div>
                        <div class="candidate-field">
                            <?= Html::activeLabel($pendaftaran, 'kota_institusi', ['class' => 'form-label']) ?>
                            <?= Html::activeTextInput($pendaftaran, 'kota_institusi', ['placeholder' => 'Kota institusi']) ?>
                            <?= Html::error($pendaftaran, 'kota_institusi') ?>
                        </div>
                        <div class="candidate-field">
                            <?= Html::activeLabel($pendaftaran, 'jenjang_pendidikan_terakhir', ['class' => 'form-label']) ?>
                            <?= Html::activeDropDownList($pendaftaran, 'jenjang_pendidikan_terakhir', Pendaftaran::optsJenjangPendidikanTerakhir(), ['prompt' => 'Pilih jenjang pendidikan']) ?>
                            <?= Html::error($pendaftaran, 'jenjang_pendidikan_terakhir') ?>
                        </div>
                        <div class="candidate-field">
                            <?= Html::activeLabel($pendaftaran, 'jurusan_asal', ['class' => 'form-label']) ?>
                            <?= Html::activeTextInput($pendaftaran, 'jurusan_asal', ['placeholder' => 'Contoh: Natural Sciences']) ?>
                            <?= Html::error($pendaftaran, 'jurusan_asal') ?>
                        </div>
                        <div class="candidate-field">
                            <?= Html::activeLabel($pendaftaran, 'nilai_akhir', ['class' => 'form-label']) ?>
                            <?= Html::activeInput('number', $pendaftaran, 'nilai_akhir', [
                                'min' => 0,
                                'step' => '0.01',
                                'placeholder' => 'Contoh: 9.42',
                                'class' => 'academic-score-input',
                            ]) ?>
                            <?= Html::error($pendaftaran, 'nilai_akhir') ?>
                        </div>
                        <div class="candidate-field">
                            <?= Html::activeLabel($pendaftaran, 'skala_nilai', ['class' => 'form-label']) ?>
                            <?= Html::activeDropDownList($pendaftaran, 'skala_nilai', Pendaftaran::optsSkalaNilai(), ['prompt' => 'Pilih skala nilai']) ?>
                            <?= Html::error($pendaftaran, 'skala_nilai') ?>
                        </div>
                        <div class="candidate-field">
                            <?= Html::activeLabel($pendaftaran, 'tahun_kelulusan', ['class' => 'form-label']) ?>
                            <?= Html::activeInput('number', $pendaftaran, 'tahun_kelulusan', [
                                'min' => 1900,
                                'max' => (int) date('Y') + 1,
                                'step' => 1,
                                'placeholder' => 'Contoh: ' . date('Y'),
                            ]) ?>
                            <?= Html::error($pendaftaran, 'tahun_kelulusan') ?>
                        </div>
                    </div>
                </section>

                <section class="card-p academic-section">
                    <div class="academic-section-heading">
                        <span class="academic-section-icon academic-section-icon-blue" aria-hidden="true">B</span>
                        <div>
                            <h2>Kemahiran Bahasa (English &amp; Indonesian)</h2>
                            <p>Masukkan skor akademik bahasa atau informasi bahasa yang Anda kuasai.</p>
                        </div>
                        <span class="academic-section-status is-optional">Standar Validasi KUI</span>
                    </div>
                    <div class="academic-language-block">
                        <div class="academic-subheading">
                            <strong>Kemampuan Bahasa Inggris</strong>
                            <span>Minimal rekomendasi / terpenuhi</span>
                        </div>
                        <div class="form-grid">
                            <div class="candidate-field">
                                <?= Html::activeLabel($pendaftaran, 'jenis_tes_bahasa_inggris', ['class' => 'form-label']) ?>
                                <?= Html::activeDropDownList($pendaftaran, 'jenis_tes_bahasa_inggris', Pendaftaran::optsJenisTesBahasaInggris(), [
                                    'prompt' => 'Pilih jenis tes',
                                    'id' => 'english-test-type',
                                ]) ?>
                                <?= Html::error($pendaftaran, 'jenis_tes_bahasa_inggris') ?>
                            </div>
                            <div class="candidate-field english-test-detail">
                                <?= Html::activeLabel($pendaftaran, 'skor_tes_bahasa_inggris', ['class' => 'form-label']) ?>
                                <?= Html::activeInput('number', $pendaftaran, 'skor_tes_bahasa_inggris', [
                                    'min' => 0,
                                    'step' => '0.01',
                                    'placeholder' => 'Contoh: 7.5',
                                    'id' => 'english-test-score',
                                ]) ?>
                                <?= Html::error($pendaftaran, 'skor_tes_bahasa_inggris') ?>
                            </div>
                            <div class="candidate-field english-test-detail">
                                <?= Html::activeLabel($pendaftaran, 'tanggal_tes_bahasa_inggris', ['class' => 'form-label']) ?>
                                <?= Html::activeInput('date', $pendaftaran, 'tanggal_tes_bahasa_inggris', []) ?>
                                <?= Html::error($pendaftaran, 'tanggal_tes_bahasa_inggris') ?>
                            </div>
                            <div class="candidate-field english-test-detail form-field-wide">
                                <?= Html::activeLabel($pendaftaran, 'nomor_sertifikat_bahasa_inggris', ['class' => 'form-label']) ?>
                                <?= Html::activeTextInput($pendaftaran, 'nomor_sertifikat_bahasa_inggris', ['placeholder' => 'Nomor sertifikat / TRF']) ?>
                                <?= Html::error($pendaftaran, 'nomor_sertifikat_bahasa_inggris') ?>
                            </div>
                        </div>
                        <small class="academic-helper-line">Skor sertifikat akan diverifikasi oleh pihak terkait.</small>
                    </div>
                    <div class="academic-language-block">
                        <div class="academic-subheading">
                            <strong>Tingkat Kemahiran Bahasa Indonesia</strong>
                            <span>Program matrikulasi BIPA tersedia</span>
                        </div>
                        <div class="form-grid">
                            <div class="candidate-field">
                                <?= Html::activeLabel($pendaftaran, 'tingkat_bahasa_indonesia', ['class' => 'form-label']) ?>
                                <?= Html::activeDropDownList($pendaftaran, 'tingkat_bahasa_indonesia', Pendaftaran::optsTingkatBahasaIndonesia(), ['prompt' => 'Pilih tingkat kemahiran']) ?>
                                <?= Html::error($pendaftaran, 'tingkat_bahasa_indonesia') ?>
                            </div>
                            <div class="candidate-field">
                                <?= Html::activeLabel($pendaftaran, 'rencana_kursus_bahasa_indonesia', ['class' => 'form-label']) ?>
                                <?= Html::activeDropDownList($pendaftaran, 'rencana_kursus_bahasa_indonesia', Pendaftaran::optsRencanaKursusBahasaIndonesia(), ['prompt' => 'Pilih rencana kursus']) ?>
                                <?= Html::error($pendaftaran, 'rencana_kursus_bahasa_indonesia') ?>
                            </div>
                        </div>
                    </div>
                </section>

                <?php if ($creditEarning): ?>
                    <section class="card-p academic-section academic-credit-section">
                        <div class="academic-section-heading">
                            <span class="academic-section-icon academic-section-icon-gold" aria-hidden="true">C</span>
                            <div>
                                <h2>Program Kunjungan &amp; Transfer Kredit (Credit Earning)</h2>
                                <p>Khusus pelamar program non-degree, tentukan keterlibatan transfer kredit.</p>
                            </div>
                            <span class="academic-section-status is-gold">Opsi Spesifik</span>
                        </div>
                        <label class="academic-transfer-toggle">
                            <?= Html::activeCheckbox($pendaftaran, 'transfer_kredit', [
                                'label' => false,
                                'value' => 1,
                                'uncheck' => 0,
                            ]) ?>
                            <span><strong>Pendaftaran ini melibatkan Transfer Kredit / Pertukaran Mahasiswa</strong><small>Centang jika Anda akan mentransfer mata kuliah yang diambil di Universitas Andalas.</small></span>
                        </label>
                        <?= Html::error($pendaftaran, 'transfer_kredit') ?>
                        <div class="form-grid academic-transfer-details">
                            <div class="candidate-field">
                                <?= Html::activeLabel($pendaftaran, 'status_mahasiswa_asal', ['class' => 'form-label']) ?>
                                <?= Html::activeDropDownList($pendaftaran, 'status_mahasiswa_asal', Pendaftaran::optsStatusMahasiswaAsal(), ['prompt' => 'Pilih status mahasiswa']) ?>
                                <?= Html::error($pendaftaran, 'status_mahasiswa_asal') ?>
                            </div>
                            <div class="candidate-field">
                                <?= Html::activeLabel($pendaftaran, 'target_kredit', ['class' => 'form-label']) ?>
                                <?= Html::activeInput('number', $pendaftaran, 'target_kredit', [
                                    'min' => 0,
                                    'step' => '0.5',
                                    'placeholder' => 'Contoh: 18',
                                ]) ?>
                                <?= Html::error($pendaftaran, 'target_kredit') ?>
                            </div>
                        </div>
                        <div class="form-grid academic-duration-field">
                            <div class="candidate-field">
                                <?= Html::activeLabel($pendaftaran, 'lama_kunjungan', ['class' => 'form-label']) ?>
                                <?= Html::activeTextInput($pendaftaran, 'lama_kunjungan', ['placeholder' => 'Contoh: 1 Semester Penuh (6 Bulan)']) ?>
                                <?= Html::error($pendaftaran, 'lama_kunjungan') ?>
                            </div>
                        </div>
                    </section>
                <?php elseif (!$degreeProgram): ?>
                    <section class="card-p academic-section">
                        <div class="academic-section-heading">
                            <span class="academic-section-icon academic-section-icon-gold" aria-hidden="true">C</span>
                            <div>
                                <h2>Rencana Kunjungan Program</h2>
                                <p>Informasi durasi program non-degree yang Anda pilih.</p>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="candidate-field">
                                <?= Html::activeLabel($pendaftaran, 'lama_kunjungan', ['class' => 'form-label']) ?>
                                <?= Html::activeTextInput($pendaftaran, 'lama_kunjungan', ['placeholder' => 'Contoh: 1 semester']) ?>
                                <?= Html::error($pendaftaran, 'lama_kunjungan') ?>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <section class="card-p academic-section academic-funding-section">
                    <div class="academic-section-heading">
                        <span class="academic-section-icon academic-section-icon-green" aria-hidden="true">$</span>
                        <div>
                            <h2>Pembiayaan Studi</h2>
                            <p>Pilih sumber pembiayaan yang direncanakan.</p>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="candidate-field">
                            <?= Html::activeLabel($pendaftaran, 'pembiayaan', ['class' => 'form-label']) ?>
                            <?= Html::activeDropDownList($pendaftaran, 'pembiayaan', Pendaftaran::optsPembiayaan(), ['prompt' => 'Pilih sumber pembiayaan']) ?>
                            <?= Html::error($pendaftaran, 'pembiayaan') ?>
                        </div>
                    </div>
                </section>
            </fieldset>

            <?php if (!$readOnly): ?>
                <div class="candidate-form-actions academic-form-actions">
                    <?= Html::a('← Kembali: Tahap 2 (Pilihan Program)', ['program'], ['class' => 'btn-secondary']) ?>
                    <?= Html::submitButton('Simpan Draft', ['class' => 'btn-secondary', 'name' => 'intent', 'value' => 'draft']) ?>
                    <?= Html::submitButton('Lanjut: Tahap 4 (Unggah Dokumen) →', ['class' => 'btn-primary', 'encode' => false]) ?>
                </div>
            <?php else: ?>
                <div class="candidate-form-actions registration-actions">
                    <?= Html::a('← Kembali: Tahap 2 (Pilihan Program)', ['program'], ['class' => 'btn-secondary']) ?>
                    <?= Html::a('Lanjut: Tahap 4 (Unggah Dokumen) →', ['dokumen'], ['class' => 'btn-primary']) ?>
                </div>
            <?php endif; ?>
            <?= Html::endForm() ?>
        </div>
        <?= $this->render('_registration_aside', ['pendaftaran' => $pendaftaran, 'activeStep' => $activeStep]) ?>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
const englishTestType = document.getElementById('english-test-type');
const englishScore = document.getElementById('english-test-score');
const updateEnglishTestFields = function () {
    const test = englishTestType.value;
    document.querySelectorAll('.english-test-detail').forEach(function (field) {
        field.hidden = !test;
    });
    const maximumScores = {
        'IELTS Academic': 9,
        'TOEFL iBT Official': 120,
        'Duolingo Test (DET)': 160
    };
    englishScore.max = maximumScores[test] || '';
    englishScore.placeholder = test === 'IELTS Academic' ? 'Contoh: 7.5' : 'Masukkan skor tes';
};
englishTestType.addEventListener('change', updateEnglishTestFields);
updateEnglishTestFields();
const transferCredit = document.getElementById('pendaftaran-transfer_kredit');
const updateTransferFields = function () {
    document.querySelectorAll('.academic-transfer-details').forEach(function (fields) {
        fields.hidden = !transferCredit || !transferCredit.checked;
    });
};
if (transferCredit) {
    transferCredit.addEventListener('change', updateTransferFields);
}
updateTransferFields();
JS); ?>
