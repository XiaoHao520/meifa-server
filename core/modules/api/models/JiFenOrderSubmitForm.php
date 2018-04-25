<?php
/**
 * Created by PhpStorm.
 * User: ganxi
 * Date: 2018-04-07
 * Time: 8:52
 */
namespace app\modules\api\models;
use app\models\JiFenOrder;

class JiFenOrderSubmitForm extends Model{
    public $store_id;
    public $user_id;
    public $jifen_order_no;
    public $pay_price;
    public $pay_type;
    public $id_pay;
    public $pay_time;
    public $addtime;



    public function rules()
    {
        return [
            [['store_id','user_id'], 'required', 'on' => "EXPRESS"],
        ];
    }
    public function attributeLabels()
    {
        return [
            'store_id' => '商家id',
            'user_id' => '用户id',
        ];
    }

    public function save(){
         if(!$this->validate()){
             return $this->getModelError();
         }
        $jifen_order_no='JF'.$this->user_id.time();
         $order=new JiFenOrder();



          if($order->isNewRecord){
              $order->jifen_order_no=$jifen_order_no;
              $order->addtime=time();
              $order->store_id=$this->store_id;
              $order->user_id=$this->user_id;
              $order->pay_price=$this->pay_price;


            return $order->saveJifenOrder();
          }


    }

}