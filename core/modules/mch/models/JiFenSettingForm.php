<?php
/**
 * Created by PhpStorm.
 * User: ganxi
 * Date: 2018-04-07
 * Time: 15:17
 */

namespace app\modules\mch\models;
use app\models\JiFenSetting;
use yii\data\Pagination;

class JiFenSettingForm extends Model
{


    public $id;
    public $charge;
    public $gifts;
    public $is_use;
    public $store_id;
    public $is_delete;
    public $addtime;
    public $jifen;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id'], 'required'],
            [['store_id', 'charge', 'gifts', 'is_use','addtime'], 'integer'],
        ];
    }

    public function getList()
    {



        if (!$this->validate())
            return $this->getModelError();

           $list = JiFenSetting::findAll(['is_delete' => 0, 'store_id' => $this->store_id]);

        return $list;
    }
    public function save(){
        if ($this->validate()) {
           $jifen = $this->jifen;
            $jifen->attributes = $this->attributes;
            if ($jifen->isNewRecord) {
                $jifen->is_delete = 0;
                $jifen->addtime = time();
            }

            return $jifen->saveJiFenSetting();
        } else {
            return $this->getModelError();
        }
    }

}