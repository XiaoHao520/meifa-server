<?php
defined('YII_RUN') or exit('Access Denied');
$version = '1.7.0';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
    <title><?= $this->title ?></title>
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/4.0.0-beta/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>/statics/admin/css/common.css?v=<?= $version ?>">
    <script>var _csrf = "<?=Yii::$app->request->csrfToken?>";</script>
    <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/popper.js/1.12.3/umd/popper.min.js"></script>
    <script src="https://cdn.bootcss.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>/statics/admin/js/common.js?v=<?= $version ?>"></script>
</head>
<body>
<?= $content ?>
</body>
</html>