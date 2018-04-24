<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%yy_service}}".
 *
 * @property integer $id
 * @property integer $goods_id
 * @property string $type_name
 * @property string $service_name
 * @property integer $is_delete
 * @property double $price
 */
class YyService extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%yy_service}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'is_delete'], 'integer'],
            [['type_name','service_name'], 'string'],
            [['price'], 'double'],
            [['price'], 'default','value'=>0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => 'Goods ID',
            'type_name' => '服务类型',
            'service_name' => '服务名',
            'is_delete' => 'Is Delete',
            'price'=>'价格'
        ];
    }
}
