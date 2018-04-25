<?php
/**
 * Created by PhpStorm.
 * User: ganxi
 * Date: 2018-04-07
 * Time: 15:12
 */
namespace app\models;
class JiFenSetting extends \yii\db\ActiveRecord{


    /**
     * This is the model class for table "{{%jifen_setting}}".
     *
     * @property integer $id
     * @property integer $charge
     * @property integer $gifts
     * @property integer $is_use
     * @property integer $store_id
     * @property integer $is_delete
*/

    public static function tableName(){
        return "{{%jifen_setting}}";
    }

    public function rules()
    {
        return [

            [['charge', 'gifts', 'is_use','is_delete','store_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'charge' => '充值',
            'gifts' => '赠送',
            'is_use' => '是否启用',
            'store_id'=>'商家ID',
            'is_delete'=>'删除'

        ];
    }
    /**
     * @return array
     */
    public function saveJiFenSetting()
    {
        if ($this->validate()) {
            if ($this->save(false)) {
                return [
                    'code' => 0,
                    'msg' => '成功'
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