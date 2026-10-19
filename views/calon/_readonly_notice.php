<?php

use yii\helpers\Html;

/** @var bool $readOnly */

if (!$readOnly) {
    return;
}
?>
<div class="form-notice registration-readonly-notice" role="status">
    Pendaftaran sudah dikirim. Tahap formulir dapat dibuka untuk diperiksa, tetapi data tidak dapat diubah.
    <?= Html::a('Lihat Status Pendaftaran', ['status']) ?>
</div>
