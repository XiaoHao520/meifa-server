<?php
/**
 * Created by PhpStorm.
 * User: peize
 * Date: 2017/12/14
 * Time: 11:32
 */

namespace app\modules\api\models\book;


use app\models\Store;
use app\models\User;
use app\models\YyForm;
use app\models\YyFormId;
use app\models\YyGoods;
use app\models\YyOrder;
use app\models\YyOrderForm;
use app\models\YyService;
use app\models\YyWechatTplMsgSender;
use app\modules\api\models\Model;

class OrderPreviewFrom extends Model
{
    public $store_id;
    public $user_id;
    public $goods_id;
    public $form_list;
    public $form_id;
    public $pay_type = 'WECHAT_PAY';
    private $wechat;
    private $order;
    private $user;
    public $datetime;

    public function search()
    {
        $goods = YyGoods::find()
            ->andWhere(['id'=>$this->goods_id,'is_delete'=>0,'status'=>1,'store_id'=>$this->store_id])
            ->asArray()
            ->one();

        $formList = YyForm::find()
            ->andWhere(['goods_id'=>$this->goods_id,'is_delete'=>0,'store_id'=>$this->store_id])
            ->orderBy('sort DESC')
            ->asArray()
            ->all();
        $goods_service=YyGoods::find()->from(YyGoods::tableName().'g')->select(['g.name','s.*'])->leftJoin(YyService::tableName().'s','g.id=s.goods_id')->andWhere(['g.is_delete'=>0,'g.store_id'=>$this->store_id,'g.status'=>1,'g.id'=>$this->goods_id,'s.is_delete'=>0])->limit(3)->asArray()->all();


        foreach ($formList AS $k => $v){
            if ($v['type'] == 'radio' || $v['type'] == 'checkbox'){
//                $formList[$k]['default'] = explode(',' , $v['default']);
                $defaultArr = explode(',' , trim($v['default'],','));
                foreach ($defaultArr AS $key => $value){
                    $defaultArr2[$key]['name'] = $value;
                    if ($key==0){
                        $defaultArr2[$key]['selected'] = true;
                    }else{
                        $defaultArr2[$key]['selected'] = false;
                    }
                }
                $formList[$k]['default'] = $defaultArr2;
            }
            if ($v['type']=='date'){
                $formList[$k]['default'] = $v['default']?:date('Y-m-d',time());
            }
        }
        return [
            'code'  => 0,
            'msg'   => '成功',
            'data'  => [
                'goods'     => $goods,
                'form_list' => $formList,
                'service_list'=>$goods_service
            ],
        ];
    }

    public function save()
    {
        $this->wechat = $this->getWechat();
        $goods = YyGoods::find()
            ->andWhere(['id'=>$this->goods_id,'is_delete'=>0,'status'=>1,'store_id'=>$this->store_id])->one();
        if (!$goods){
            return [
              'code'    => 1,
              'msg'     => '商品不存在',
            ];
        }

         $is_exist=YyOrder::find()->where(['goods_id'=>$this->goods_id,'datetime'=>$this->datetime])->exists();

         if($is_exist){
             return [
                 'code'  => 1,
                 'msg'   => '师傅这个时段已经被预约,请更换时段或者日期',
                 'is_exist'=>$is_exist
             ];
         }




        $p = \Yii::$app->db->beginTransaction();

        $this->user = User::findOne(['id' => $this->user_id, 'type' => 1, 'is_delete' => 0]);

        $order = new YyOrder();
        $order->store_id = $this->store_id;
        $order->goods_id = $goods->id;
        $order->user_id  = $this->user_id;
        $order->order_no = $this->getOrderNo();
        $order->datetime=$this->datetime;
        $order->total_price = $goods->price;
        $order->pay_price = $goods->price;
        $order->is_pay = 0;
        $order->is_use = 0;
        $order->is_comment = 0;
        $order->addtime = time();
        $order->is_delete = 0;
        $order->form_id = $this->form_id;

        if ($order->save()) {
            $goods->sales ++;
            $goods->save();
            foreach ($this->form_list AS $key => $value)
            {

                $formList = new YyOrderForm();
                $formList->store_id = $this->store_id;
                $formList->goods_id = $goods->id;
                $formList->user_id  = $this->user_id;
                $formList->order_id = $order->id;
                $formList->key      = $value['key'];
                $formList->value    = $value['value'];
                $formList->is_delete= 0;
                $formList->addtime  = time();

                if (!$formList->save()){
                    $p->rollBack();
                    return [
                        'code'  => 1,
                        'msg'   => '订单提交失败，请稍后重试',
                    ];
                }
            }

            if ($order->pay_price <= 0){
                $order->is_pay = 1;
                $order->pay_type = 1;
                $order->pay_time = time();
                if ($order->save()){
                    $wechat_tpl_meg_sender = new YyWechatTplMsgSender($order->store_id, $order->id, $this->wechat);
                    $wechat_tpl_meg_sender->payMsg();

                    $p->commit();
                    return [
                        'code'  => 0,
                        'msg'   => '订单提交成功',
                        'type'  => 1,
                    ];
                }else{
                    $p->rollBack();
                    return [
                        'code'  => 1,
                        'msg'   => '订单提交失败，请稍后重试',
                    ];
                }
            }

            $this->order = $order;
            $goods_names = mb_substr($goods->name, 0, 32, 'utf-8');
            $pay_data = [];
            $res = null;
            if ($this->pay_type == 'WECHAT_PAY') {
                $res = $this->unifiedOrder($goods_names);
                if (isset($res['code']) && $res['code'] == 1) {
                    return $res;
                }

                //记录prepay_id发送模板消息用到
//                YyFormId::addFormId([
//                    'store_id' => $this->store_id,
//                    'user_id' => $this->user->id,
//                    'wechat_open_id' => $this->user->wechat_open_id,
//                    'form_id' => $res['prepay_id'],
//                    'type' => 'prepay_id',
//                    'order_no' => $this->order->order_no,
//                ]);
                $order->form_id = $res['prepay_id'];
                $order->save();
                $pay_data = [
                    'appId' => $this->wechat->appId,
                    'timeStamp' => '' . time(),
                    'nonceStr' => md5(uniqid()),
                    'package' => 'prepay_id=' . $res['prepay_id'],
                    'signType' => 'MD5',
                ];
                $pay_data['paySign'] = $this->wechat->pay->makeSign($pay_data);
//                $this->setReturnData($this->order);
//                return [
//                    'code' => 0,
//                    'msg' => 'success',
//                    'data' => (object)$pay_data,
//                    'res' => $res,
//                    'body' => $goods_names,
//                ];
            }



            $p->commit();
            return [
                'code' => 0,
                'msg' => '订单提交成功',
                'data' => (object)$pay_data,
                'res' => $res,
                'body' => $goods_names,
                'type' => 2,
            ];

        }else{
            $p->rollBack();
            return $this->getModelError($order);
        }
    }

    /**
     * @return null|string
     * 生成订单号
     */
    public function getOrderNo()
    {
        $store_id = empty($this->store_id) ? 0 : $this->store_id;
        $order_no = null;
        while (true) {
            $order_no = 'Y'.date('YmdHis') . rand(10000, 99999);
            $exist_order_no = YyOrder::find()->where(['order_no' => $order_no])->exists();
            if (!$exist_order_no)
                break;
        }
        return $order_no;
    }

    /**
     * @param $goods_names
     * @return array
     * 统一下单
     */
    private function unifiedOrder($goods_names)
    {
        $res = $this->wechat->pay->unifiedOrder([
            'body' => $goods_names,
            'out_trade_no' => $this->order->order_no,
            'total_fee' => $this->order->pay_price * 100,
            'notify_url' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/pay-notify.php',
            'trade_type' => 'JSAPI',
            'openid' => $this->user->wechat_open_id,
        ]);
        if (!$res)
            return [
                'code' => 1,
                'msg' => '支付失败',
            ];
        if ($res['return_code'] != 'SUCCESS') {
            return [
                'code' => 1,
                'msg' => '支付失败，' . (isset($res['return_msg']) ? $res['return_msg'] : ''),
                'res' => $res,
            ];
        }
        if ($res['result_code'] != 'SUCCESS') {
            if ($res['err_code'] == 'INVALID_REQUEST') {//商户订单号重复
                $this->order->order_no = $this->getOrderNo();
                $this->order->save();
                return $this->unifiedOrder($goods_names);
            } else {
                return [
                    'code' => 1,
                    'msg' => '支付失败，' . (isset($res['err_code_des']) ? $res['err_code_des'] : ''),
                    'res' => $res,
                ];
            }
        }
        return $res;
    }


    public function payData($id)
    {
        $this->wechat = $this->getWechat();
        $this->user = User::findOne(['id' => $this->user_id, 'type' => 1, 'is_delete' => 0]);
        $order = YyOrder::find()
            ->andWhere([
                'is_delete' => 0,
                'store_id' => $this->store_id,
                'user_id' => $this->user_id,
                'is_cancel' => 0,
                'id' => $id,
                'is_pay'    => 0,
            ])->one();
        if (!$order){
            return [
                'code'  => 1,
                'msg'   => '订单不存在，或已支付',
            ];
        }

        $this->order = $order;


        $goods = YyGoods::findOne(['id'=>$order->goods_id]);

//        if (!$goods){
//            return [
//                'code'  => 1,
//                'msg'   => '订单不存在，或已支付',
//            ];
//        }

        $goods_names = mb_substr($goods->name, 0, 32, 'utf-8');
//        $pay_data = [];
//        $res = null;
        if ($this->pay_type == 'WECHAT_PAY') {

            $res = $this->unifiedOrder($goods_names);
            if (isset($res['code']) && $res['code'] == 1) {
                return $res;
            }

            //记录prepay_id发送模板消息用到
//            YyFormId::addFormId([
//                'store_id' => $this->store_id,
//                'user_id' => $this->user->id,
//                'wechat_open_id' => $this->user->wechat_open_id,
//                'form_id' => $res['prepay_id'],
//                'type' => 'prepay_id',
//                'order_no' => $this->order->order_no,
//            ]);
            $order->form_id = $res['prepay_id'];
            $order->save();

            $pay_data = [
                'appId' => $this->wechat->appId,
                'timeStamp' => '' . time(),
                'nonceStr' => md5(uniqid()),
                'package' => 'prepay_id=' . $res['prepay_id'],
                'signType' => 'MD5',
            ];
            $pay_data['paySign'] = $this->wechat->pay->makeSign($pay_data);
//                $this->setReturnData($this->order);
            return [
                'code' => 0,
                'msg' => 'success',
                'data' => (object)$pay_data,
                'res' => $res,
                'body' => $goods_names,
            ];
        }
    }

}