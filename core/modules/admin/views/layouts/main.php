<?php
defined('YII_RUN') or exit('Access Denied');
$version = '1.7.0';
$url_manager = Yii::$app->urlManager;
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
<div class="sidebar">
    <ul>
        <?php if (Yii::$app->admin->id == 1): ?>
            <li>
                <span>用户管理</span>
                <ul>
                    <li><a href="<?= $url_manager->createUrl(['admin/user/index']) ?>">用户列表</a></li>
                    <li><a href="<?= $url_manager->createUrl(['admin/user/edit']) ?>">新增用户</a></li>
                </ul>
            </li>
        <?php endif; ?>
        <li>
            <span>小程序</span>
            <ul>
                <li><a href="<?= $url_manager->createUrl(['admin/app/index']) ?>">我的小程序</a></li>
            </ul>
        </li>
    </ul>
    <a href="<?= $url_manager->createUrl(['admin/passport/logout']) ?>">注销</a>
</div>
<?= $content ?>
</body>
</html>