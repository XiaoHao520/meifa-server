<?php
/**
 * Created by PhpStorm.
 * User: ganxi
 * Date: 2018-04-07
 * Time: 8:55
 */
namespace app\models;
class JiFenOrder extends \yii\db\ActiveRecord{
    /**
     * This is the model class for table "{{%jifen_order}}".
     *
     * @property integer $id
     * @property integer $store_id
     * @property integer $user_id
     * @property string $jifen_order_no
     * @property string $pay_price
     * @property integer $is_pay
     * @property integer $pay_type
     * @property integer $pay_time
     * @property integer $addtime
     */
    public static function tableName(){
        return "{{%jifen_order}}";
    }

    public function rules()
    {
        return [
            [['store_id', 'user_id', 'jifen_order_no'], 'required'],
            [['store_id', 'user_id', 'is_pay', 'pay_type', 'pay_time','addtime',], 'integer'],
            [['pay_price','jifen_order_no'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => 'Store ID',
            'user_id' => '用户id',
            'jifen_order_no' => '订单号',
            'pay_price' => '实际支付总费用(含运费）',
            'is_pay' => '支付状态：0=未支付，1=已支付',
            'pay_type' => '支付方式：1=微信支付',
            'pay_time' => '支付时间',
            'addtime' => 'Addtime',
        ];
    }

    public function saveJifenOrder(){
        if ($this->validate()) {
            if ($this->save(false)) {
                return [
                    'code' => 0,
                    'msg' => '成功',
                    'order_no'=>$this->jifen_order_no
                ];
            } else {
                return [
                    'code' => 1,
                    'msg' => '失败'
                ];
            }
        } else {
            return (new Model())->getModelError($this);
        }

    }
}