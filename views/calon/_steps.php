<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var int $activeStep */
/** @var bool $readOnly */

$steps = [
    ['Data Diri', 'form', 'Identitas'],
    ['Program', 'program', 'Pilihan Studi'],
    ['Akademik', 'akademik', 'Riwayat Studi'],
    ['Dokumen', 'dokumen', 'Unggah Berkas'],
    ['Bantuan Visa', 'visa', 'Pengurusan'],
    ['Ringkasan & Kirim', 'ringkasan', 'Final Review'],
];
$progress = (int) round(($activeStep / count($steps)) * 100);
?>

<div class="registration-breadcrumb">
    <?= Html::a('Beranda', ['index']) ?>
    <span aria-hidden="true">›</span>
    <?= Html::a('Formulir Registrasi', ['form']) ?>
    <span aria-hidden="true">›</span>
    <strong>Tahap <?= $activeStep ?>: <?= Html::encode($steps[$activeStep - 1][0]) ?></strong>
</div>
<nav class="wizard-steps card-p" aria-label="Tahapan formulir pendaftaran">
    <div class="wizard-heading">
        <div>
            <span class="eyebrow">LANGKAH PENDAFTARAN 2026/2027</span>
            <strong>Tahap <?= $activeStep ?>: <?= Html::encode($steps[$activeStep - 1][0]) ?></strong>
        </div>
        <div class="wizard-progress">
            <span><?= $progress ?>% Selesai</span>
            <span class="wizard-progress-track"><span style="width: <?= $progress ?>%"></span></span>
        </div>
    </div>
    <?php foreach ($steps as $index => [$label, $route]): ?>
        <?php $step = $index + 1; ?>
        <div class="wizard-step <?= $step === $activeStep ? 'is-current' : '' ?> <?= $step < $activeStep ? 'is-done' : '' ?>">
            <?php if ($readOnly || $step <= $activeStep): ?>
                <a href="<?= Html::encode(\yii\helpers\Url::to([$route])) ?>">
                    <span class="step-number"><?= $step < $activeStep ? '&#10003;' : $step ?></span>
                    <span class="wizard-step-copy"><strong><?= Html::encode($step . '. ' . $label) ?></strong><small><?= Html::encode($steps[$index][2]) ?></small></span>
                </a>
            <?php else: ?>
                <span class="wizard-step-locked">
                    <span class="step-number"><?= $step ?></span>
                    <span class="wizard-step-copy"><strong><?= Html::encode($step . '. ' . $label) ?></strong><small><?= Html::encode($steps[$index][2]) ?></small></span>
                </span>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</nav>
