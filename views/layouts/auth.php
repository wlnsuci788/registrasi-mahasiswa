<?php

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var string $content */

AppAsset::register($this);
$this->registerCsrfMetaTags();
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <title><?= Html::encode($this->title) ?> | Portal Mahasiswa Asing UNAND</title>
    <?php $this->head() ?>
</head>
<body class="auth-body <?= Html::encode($this->params['authPageClass'] ?? '') ?>">
<?php $this->beginBody() ?>
<?= Alert::widget() ?>
<div class="auth-wrap <?= Html::encode($this->params['authWrapClass'] ?? '') ?>">
    <?= $content ?>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
