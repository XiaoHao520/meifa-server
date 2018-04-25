<?php
defined('YII_RUN') or exit('Access Denied');

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/27
 * Time: 11:14
 */

use yii\widgets\LinkPager;

$urlManager = Yii::$app->urlManager;
$this->title = '积分设置';
$this->params['active_nav_group'] = 2;
?>

<div class="panel mb-3">
    <div class="panel-header">
        <span><?= $this->title ?></span>
        <ul class="nav nav-right">
            <li class="nav-item">
                <a class="nav-link" href="<?= $urlManager->createAbsoluteUrl(['mch/user/jifen-edit']) ?>">添加积分规则</a>
            </li>
        </ul>
    </div>
    <div class="panel-body">
        <table class="table table-bordered bg-white">
            <thead>
            <tr>
                <th>ID</th>
                <th>充值</th>
                <th>赠送</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($jifen_list as $index => $jifen): ?>
                <tr>
                    <td><?= $jifen['id'] ?></td>
                    <td><?= $jifen['charge'] ?></td>

                    <td><?= $jifen['gifts'] ?></td>
                    </td>
                    <td>
                        <a class="btn btn-sm btn-primary"
                           href="<?= $urlManager->createUrl(['mch/user/jifen-edit', 'id' => $jifen['id']]) ?>">修改</a>
                       <!-- <a class="btn btn-sm btn-danger del"
                           href="<?/*= $urlManager->createUrl(['mch/user/jifen-del', 'id' => $jifen['id']]) */?>">删除</a>-->
                    </td>
                </tr>

            <?php endforeach; ?>
            </tbody>

        </table>
    </div>
</div>

<script>
    $(document).on('click', '.del', function () {
        if (confirm("是否删除？")) {
            $.ajax({
                url: $(this).attr('href'),
                type: 'get',
                dataType: 'json',
                success: function (res) {
                    alert(res.msg);
                    if (res.code == 0) {
                        window.location.reload();
                    }
                }
            });
        }
        return false;
    });
</script>
<script>
    $(document).ready(function () {
        var clipboard = new Clipboard('.copy');
        clipboard.on('success', function (e) {
            $.myAlert({
                title: '提示',
                content: '复制成功'
            });
        });
        clipboard.on('error', function (e) {
            $.myAlert({
                title: '提示',
                content: '复制失败，请手动复制。链接为：' + e.text
            });
        });
    })
</script>