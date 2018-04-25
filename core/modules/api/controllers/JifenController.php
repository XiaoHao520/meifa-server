<?php
/**
 * Created by PhpStorm.
 * User: ganxi
 * Date: 2018-04-07
 * Time: 8:47
 */
namespace app\modules\api\controllers;
use app\models\JiFenOrder;
use app\models\JiFenSetting;
use app\models\User;
use app\modules\api\models\JifenOrderPayDataForm;
use app\modules\api\models\JiFenOrderSubmitForm;
use yii\db\Expression;

class JifenController extends Controller{


     public function actionSubmit(){

           $form=new JiFenOrderSubmitForm();
           $data=\Yii::$app->request->post();
           $form->user_id=$data['user_id'];
           $form->store_id=$this->store->id;
           $form->pay_price=$data['pay_price'];
           $data=$form->save();
           $this->renderJson($data);

     }

     public function actionPayData(){
         $form = new JifenOrderPayDataForm();
         $form->attributes = \Yii::$app->request->get();
         $form->store_id = $this->store->id;
         $form->user = \Yii::$app->user->identity;
         $this->renderJson($form->search());
     }

     public function actionRules(){
        $list = JiFenSetting::find()->where(array('is_delete' => 0, 'store_id' => $this->store_id,'is_use'=>1))->asArray()->all();
        $this->renderJson(['code' => 0,
            'msg' => '',
            'data' => $list]);
     }
     public function actionUpdate(){
        $form = \Yii::$app->request->get();
        $res= JiFenOrder::updateAll(['is_pay'=>1],['jifen_order_no'=>$form['order_no']]);
        $this->renderJson(['res'=>$res]);
     }
     public function actionUpdateJifen(){
         $form=\Yii::$app->request->get();
         $res=\Yii::$app->db->createCommand()->update('hjmall_user',array('integral'=>new Expression('integral+'.$form['jifen'])),'id=:id',array(':id'=>$form['user_id']))->execute();
         $this->renderJson(['res'=>$res]);

     }

}