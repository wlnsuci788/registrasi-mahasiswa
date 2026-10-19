<?php

use app\models\Pendaftaran;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Pendaftaran $pendaftaran */
/** @var array<string, string> $visaData */
/** @var int $activeStep */
/** @var bool $readOnly */

$this->title = 'Bantuan Visa';
?>

<div class="cal-dashboard registration-dashboard">
    <?= $this->render('_steps', ['activeStep' => $activeStep, 'readOnly' => $readOnly]) ?>
    <div class="registration-grid">
        <div class="registration-main">
    <div class="cal-greeting">
        <div>
            <div class="eyebrow">TAHAP 5 DARI 6</div>
            <h1>Bantuan Visa &amp; Izin Belajar</h1>
            <p class="muted">Pilih apakah Anda membutuhkan bantuan pengurusan visa dari Kantor Urusan Internasional.</p>
        </div>
    </div>

    <?= Html::beginForm(['visa'], 'post', ['class' => 'candidate-form']) ?>
    <?= $this->render('_readonly_notice', ['readOnly' => $readOnly]) ?>
    <?= Html::errorSummary($pendaftaran, ['class' => 'card-p form-errors']) ?>
    <fieldset class="registration-readonly-fields" <?= $readOnly ? 'disabled' : '' ?>>
    <section class="card-p">
        <fieldset class="candidate-field visa-choice-group">
            <legend>Apakah Anda membutuhkan bantuan pengurusan visa dari International Office?</legend>
            <div class="choice-cards visa-choice-cards">
                <label class="choice-card <?= $pendaftaran->pilihan_visa === Pendaftaran::PILIHAN_VISA_YA ? 'is-selected' : '' ?>">
                    <?= Html::activeRadio($pendaftaran, 'pilihan_visa', [
                        'value' => Pendaftaran::PILIHAN_VISA_YA,
                        'label' => false,
                        'uncheck' => null,
                        'id' => 'visa-choice-help',
                        'required' => true,
                    ]) ?>
                    <span>
                        <strong>Ya, saya butuh bantuan</strong>
                        <small>International Office membantu proses dan memberi arahan pengurusan visa.</small>
                    </span>
                </label>
                <label class="choice-card <?= $pendaftaran->pilihan_visa === Pendaftaran::PILIHAN_VISA_TIDAK ? 'is-selected' : '' ?>">
                    <?= Html::activeRadio($pendaftaran, 'pilihan_visa', [
                        'value' => Pendaftaran::PILIHAN_VISA_TIDAK,
                        'label' => false,
                        'uncheck' => null,
                        'id' => 'visa-choice-self',
                    ]) ?>
                    <span>
                        <strong>Tidak, saya mengurus sendiri</strong>
                        <small>Saya akan mengurus visa secara mandiri dan tidak memerlukan bantuan pengajuan.</small>
                    </span>
                </label>
            </div>
            <?= Html::error($pendaftaran, 'pilihan_visa') ?>
        </fieldset>

        <div id="visa-details" class="visa-details" <?= $pendaftaran->jalur !== Pendaftaran::JALUR_ASING || $pendaftaran->pilihan_visa !== Pendaftaran::PILIHAN_VISA_YA ? 'hidden' : '' ?>>
            <h2>Rincian Pengajuan Visa</h2>
            <p class="muted">Isi rincian berikut agar International Office dapat menindaklanjuti permohonan bantuan Anda.</p>
            <?php if ($pendaftaran->jalur === Pendaftaran::JALUR_WNI): ?>
                <p class="muted">Pengajuan visa pelajar hanya berlaku untuk pendaftar warga negara asing. Pilihan visa diatur otomatis menjadi Tidak.</p>
            <?php endif; ?>
            <div class="form-grid">
                <div class="candidate-field">
                    <?= Html::activeLabel($pendaftaran, 'jenis_visa', ['class' => 'form-label']) ?>
                    <?= Html::activeTextInput($pendaftaran, 'jenis_visa', [
                        'placeholder' => 'Contoh: Visa pelajar C316',
                        'required' => true,
                    ]) ?>
                    <?= Html::error($pendaftaran, 'jenis_visa') ?>
                </div>
            </div>
            <?php if (!$pendaftaran->no_paspor): ?>
                <p class="form-notice">Nomor paspor belum diisi. Kembali ke tahap Data Diri untuk melengkapinya sebelum mengajukan bantuan visa.</p>
            <?php endif; ?>
            <div class="form-grid">
                <div class="candidate-field">
                    <label for="visa-masa-berlaku-paspor">Masa berlaku paspor</label>
                    <?= Html::input('date', 'VisaForm[masa_berlaku_paspor]', $visaData['masa_berlaku_paspor'] ?? '', [
                        'id' => 'visa-masa-berlaku-paspor',
                        'required' => true,
                    ]) ?>
                </div>
                <div class="candidate-field">
                    <label for="visa-tujuan">Tujuan pengajuan</label>
                    <?= Html::textInput('VisaForm[tujuan]', $visaData['tujuan'] ?? '', [
                        'id' => 'visa-tujuan',
                        'placeholder' => 'Belajar / mengikuti program',
                        'required' => true,
                    ]) ?>
                </div>
                <div class="candidate-field">
                    <label for="visa-tanggal-kedatangan">Perkiraan tanggal kedatangan</label>
                    <?= Html::input('date', 'VisaForm[tanggal_kedatangan]', $visaData['tanggal_kedatangan'] ?? '', [
                        'id' => 'visa-tanggal-kedatangan',
                        'required' => true,
                    ]) ?>
                </div>
                <div class="candidate-field">
                    <label for="visa-kota-kedatangan">Kota / bandara kedatangan</label>
                    <?= Html::textInput('VisaForm[kota_kedatangan]', $visaData['kota_kedatangan'] ?? '', [
                        'id' => 'visa-kota-kedatangan',
                        'placeholder' => 'Contoh: Bandara Internasional Minangkabau',
                        'required' => true,
                    ]) ?>
                </div>
                <div class="candidate-field form-field-wide">
                    <label for="visa-alamat">Alamat selama di Padang</label>
                    <?= Html::textInput('VisaForm[alamat_selama_di_padang]', $visaData['alamat_selama_di_padang'] ?? '', [
                        'id' => 'visa-alamat',
                        'placeholder' => 'Alamat tempat tinggal selama mengikuti program',
                        'required' => true,
                    ]) ?>
                </div>
            </div>
        </div>
    </section>
    </fieldset>
    <?php if (!$readOnly): ?>
    <div class="candidate-form-actions">
        <?= Html::a('← Kembali ke Dokumen', ['dokumen'], ['class' => 'btn-secondary']) ?>
        <?= Html::submitButton('Simpan &amp; Lanjut ke Ringkasan →', ['class' => 'btn-primary', 'encode' => false]) ?>
    </div>
    <?php else: ?>
        <div class="candidate-form-actions registration-actions">
            <?= Html::a('← Kembali: Tahap 4 (Dokumen)', ['dokumen'], ['class' => 'btn-secondary']) ?>
            <?= Html::a('Lanjut: Tahap 6 (Ringkasan & Kirim) →', ['ringkasan'], ['class' => 'btn-primary']) ?>
        </div>
    <?php endif; ?>
    <?= Html::endForm() ?>
        </div>
        <?= $this->render('_registration_aside', ['pendaftaran' => $pendaftaran, 'activeStep' => $activeStep]) ?>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
const visaDetails = document.getElementById('visa-details');
const isWni = <?= json_encode($pendaftaran->jalur === Pendaftaran::JALUR_WNI) ?>;
const visaRadios = document.querySelectorAll('input[name="Pendaftaran[pilihan_visa]"]');
const updateVisaFields = function () {
    const selectedChoice = document.querySelector('input[name="Pendaftaran[pilihan_visa]"]:checked');
    const needsHelp = !isWni && selectedChoice && selectedChoice.value === 'Ya';
    visaDetails.hidden = !needsHelp;
    visaDetails.querySelectorAll('[required]').forEach(function (field) {
        field.disabled = !needsHelp;
    });
    visaDetails.querySelectorAll('input:not([required])').forEach(function (field) {
        field.disabled = !needsHelp;
    });
    visaRadios.forEach(function (radio) {
        const card = radio.closest('.choice-card');
        card.classList.toggle('is-selected', radio.checked);
    });
};
<?php if ($pendaftaran->jalur === Pendaftaran::JALUR_WNI): ?>
document.getElementById('visa-choice-self').checked = true;
visaRadios.forEach(function (radio) {
    radio.disabled = true;
});
<?php endif; ?>
visaRadios.forEach(function (radio) {
    radio.addEventListener('change', updateVisaFields);
});
updateVisaFields();
JS); ?>
