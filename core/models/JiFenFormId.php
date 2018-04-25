<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%form_id}}".
 *
 * @property integer $id
 * @property integer $store_id
 * @property integer $user_id
 * @property string $openid
 * @property string $jifen_form_id
 * @property string $jifen_order_no
 * @property string $type
 * @property integer $send_count
 * @property integer $addtime
 */
class JiFenFormId extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%jifen_form_id}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'user_id'], 'required'],
            [['store_id', 'user_id', 'send_count', 'addtime'], 'integer'],
            [['openid', 'jifen_form_id', 'jifen_order_no', 'type'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => '店铺id',
            'user_id' => '用户id',
            'openid' => '微信openid',
            'jifen_form_id' => 'Form ID',
            'jifen_order_no' => 'Order No',
            'type' => '可选值：form_id或prepay_id',
            'send_count' => '使用次数',
            'addtime' => 'Addtime',
        ];
    }

    /**
     * @param array $args
     * [
     * 'store_id'=>'店铺id',
     * 'user_id'=>'用户id',
     * 'openid'=>'微信openid',
     * 'form_id'=>'Form Id 或 Prepay Id'
     * 'type'=>'form_id或prepay_id'
     * ]
     */
    public static function addFormId($args)
    {
        if (!isset($args['jifen_form_id']) || !$args['jifen_form_id'])
            return false;
        $model = new JiFenFormId();
        $model->attributes = $args;
        $model->addtime = time();
        return $model->save();
    }
}
