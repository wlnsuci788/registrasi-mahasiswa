<?php

use app\models\DokumenPersyaratan;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var array[] $dokumen */
/** @var DokumenPersyaratan $formModel */
/** @var int|null $editId */

$this->title = 'Kelola Dokumen Persyaratan';
$isEditing = $editId !== null;
?>
<?= $this->render('_demo_notice') ?>
<div class="io-breadcrumb">INTERNATIONAL OFFICE <span>›</span> KELOLA DOKUMEN PERSYARATAN</div>
<section class="io-page-heading">
    <div>
        <h1>Kelola Dokumen Persyaratan Pendaftaran</h1>
        <p class="muted">Atur dokumen wajib dan opsional untuk pendaftar program Degree dan Non-degree.</p>
    </div>
    <a class="io-button io-button-primary" href="#form-dokumen">＋ Tambah Dokumen Persyaratan</a>
</section>
<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="io-alert io-alert-success"><?= Html::encode(Yii::$app->session->getFlash('success')) ?></div>
<?php endif; ?>

<section class="io-panel io-requirements-panel">
    <?php if ($dokumen): ?>
        <div class="io-table-wrap">
            <table class="io-table">
                <thead><tr><th>Nama dokumen</th><th>Berlaku untuk</th><th>Sifat</th><th>Aktif</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($dokumen as $document): ?>
                    <tr>
                        <td><strong><?= Html::encode($document['nama_dokumen']) ?></strong></td>
                        <td><span class="io-tag"><?= Html::encode($document['berlaku_untuk']) ?></span></td>
                        <td><span class="<?= $document['wajib'] ? 'io-required' : 'io-optional' ?>"><?= $document['wajib'] ? 'Wajib' : 'Opsional' ?></span></td>
                        <td>
                            <form method="post">
                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                                <?= Html::hiddenInput('operation', 'toggle') ?>
                                <?= Html::hiddenInput('dokumen_id', (string) $document['dokumen_id']) ?>
                                <button class="io-toggle <?= $document['active'] ? 'is-on' : '' ?>" type="submit" aria-label="<?= $document['active'] ? 'Nonaktifkan' : 'Aktifkan' ?> <?= Html::encode($document['nama_dokumen']) ?>">
                                    <span></span><span class="visually-hidden"><?= $document['active'] ? 'Aktif' : 'Nonaktif' ?></span>
                                </button>
                            </form>
                        </td>
                        <td class="io-actions">
                            <?= Html::a('Ubah', ['/io/dokumen-persyaratan', 'edit' => $document['dokumen_id'], '#' => 'form-dokumen'], ['class' => 'io-text-action']) ?>
                            <form method="post" onsubmit="return confirm('Arsipkan dokumen persyaratan ini?');">
                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                                <?= Html::hiddenInput('operation', 'archive') ?>
                                <?= Html::hiddenInput('dokumen_id', (string) $document['dokumen_id']) ?>
                                <button class="io-text-action io-text-danger" type="submit">Arsipkan</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="io-table-foot">Menampilkan <?= Html::encode((string) count($dokumen)) ?> dokumen persyaratan</div>
    <?php else: ?>
        <div class="io-empty">Belum ada dokumen persyaratan. Tambahkan dokumen melalui formulir berikut.</div>
    <?php endif; ?>
</section>

<section class="io-panel io-requirement-form" id="form-dokumen">
    <div class="io-panel-heading">
        <div><h2><?= $isEditing ? 'Ubah Dokumen Persyaratan' : 'Tambah Dokumen Persyaratan' ?></h2><p class="muted">Ketentuan ini akan digunakan pada formulir pendaftaran calon mahasiswa.</p></div>
        <?php if ($isEditing): ?>
            <?= Html::a('Batal', ['/io/dokumen-persyaratan'], ['class' => 'io-button io-button-light']) ?>
        <?php endif; ?>
    </div>
    <?php $form = ActiveForm::begin(['options' => ['class' => 'io-requirement-fields']]); ?>
        <?= Html::hiddenInput('operation', 'save') ?>
        <?= Html::hiddenInput('dokumen_id', $isEditing ? (string) $editId : '') ?>
        <?= $form->field($formModel, 'nama_dokumen')->textInput([
            'name' => 'nama_dokumen',
            'maxlength' => true,
            'placeholder' => 'Contoh: Paspor Internasional',
        ]) ?>
        <div class="form-group">
            <label class="control-label" for="io-berlaku">Berlaku untuk</label>
            <?= Html::dropDownList(
                'berlaku_untuk',
                $formModel->berlaku_untuk,
                DokumenPersyaratan::optsBerlakuUntuk(),
                ['id' => 'io-berlaku', 'class' => 'form-select'],
            ) ?>
            <?= Html::error($formModel, 'berlaku_untuk', ['class' => 'invalid-feedback d-block']) ?>
        </div>
        <div class="form-group io-checkbox-field">
            <?= Html::checkbox('wajib', (bool) $formModel->wajib, ['value' => '1', 'id' => 'io-wajib']) ?>
            <label for="io-wajib">Dokumen ini wajib diunggah</label>
        </div>
        <button class="io-button io-button-primary" type="submit"><?= $isEditing ? 'Simpan Perubahan' : 'Simpan Dokumen' ?></button>
    <?php ActiveForm::end(); ?>
</section>
