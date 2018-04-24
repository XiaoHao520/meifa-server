<?php
/**
 * Created by IntelliJ IDEA.
 * User: luwei
 * Date: 2017/10/16
 * Time: 15:43
 */

namespace app\modules\api\controllers;


use app\modules\api\models\MiaoshaGoodsListForm;
use app\modules\api\models\MiaoshaListForm;

class MiaoshaController extends Controller
{
    //今日秒杀安排列表
    public function actionList()
    {
        $form = new MiaoshaListForm();
        $form->store_id = $this->store->id;
        $form->time = intval(date('H'));
        $form->date = date('Y-m-d');
        $this->renderJson($form->search());
    }

    //秒杀商品列表
    public function actionGoodsList()
    {
        $form = new MiaoshaGoodsListForm();
        $form->attributes = \Yii::$app->request->get();
        $form->store_id = $this->store->id;
        $form->date = date('Y-m-d');
        $this->renderJson($form->search());
    }
}