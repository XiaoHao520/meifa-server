<?php
$urlManager = Yii::$app->urlManager;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
    <title><?= $this->title ?></title>
    <link href="//cdn.bootcss.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" rel="stylesheet">
    <style>
    </style>
    <script src="//cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script src="//cdn.bootcss.com/vue/2.3.4/vue.min.js"></script>
    <script src="//cdn.bootcss.com/tether/1.4.0/js/tether.min.js"></script>
    <script src="//cdn.bootcss.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>
    <script>
        var _csrf = "<?=Yii::$app->request->csrfToken?>";

    </script>
</head>
<body>
<?= $content ?>
<script>
    $(document).ready(function () {
        $("form[method=post]").each(function () {
            if (this._csrf == undefined)
                $(this).append('<input name="_csrf" value="' + _csrf + '" type="hidden">');
        });

        $(document).on("submit", ".auto-submit-form", function () {
            var form = $(this);
            var btn = form.find(".submit-btn");
            var error = form.find(".form-error");
            var success = form.find(".form-success");

        });
    });
</script>
</body>
</html>