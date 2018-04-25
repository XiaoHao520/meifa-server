<?php
defined('YII_RUN') or exit('Access Denied');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/27
 * Time: 11:36
 */

$urlManager = Yii::$app->urlManager;
$this->title = '积分充值编辑';
$this->params['active_nav_group'] = 3;
?>

<div class="panel mb-3">
    <div class="panel-header"><?= $this->title ?></div>
    <div class="panel-body">
        <form class="auto-form" method="post" return="<?= $urlManager->createUrl(['mch/user/jifen-list']) ?>">


            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">充值金额满：</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" type="text" name="model[charge]" value="<?= $list['charge'] ?>">
                </div>
            </div>


            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">赠送：</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" type="text" name="model[gifts]" value="<?= $list['gifts'] ?>">
                </div>
            </div>



            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">是否启用</label>
                </div>
                <div class="col-sm-6">
                    <label class="radio-label">
                        <input id="radio1" <?= $list['is_use'] == 1 ? 'checked' : 'checked' ?>
                               value="1"
                               name="model[is_use]" type="radio" class="custom-control-input">
                        <span class="label-icon"></span>
                        <span class="label-text">启用</span>
                    </label>
                    <label class="radio-label">
                        <input id="radio2" <?= $list['is_use'] == 0 ? 'checked' : null ?>
                               value="0"
                               name="model[is_use]" type="radio" class="custom-control-input">
                        <span class="label-icon"></span>
                        <span class="label-text">停用</span>
                    </label>
                </div>
            </div>



            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary auto-form-btn" href="javascript:">保存</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).on('change', '.parent', function () {
        var p = $(this).val();
        if (p == '0') {
            $('.advert').show();
        } else {
            $('input[name="model[advert_url]"]').val('').trigger('change');
            $('input[name="model[advert_pic]"]').val('').trigger('change');
            $('input[name="model[advert_pic]"]').next('.image-picker-view').css('background-image', 'url("")');
            $('.advert').hide();
        }
    })
</script>

