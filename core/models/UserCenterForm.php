<?php
/**
 * Created by IntelliJ IDEA.
 * User: luwei
 * Date: 2018/1/25
 * Time: 11:37
 */

namespace app\models;

class UserCenterForm extends Model
{
    public $store_id;
    public $user_id;
    public $data;
    public $store;

    public function rules()
    {
        return [
            [['data'], 'required'],
            [['data'], 'string'],
        ];
    }

    /**
     * @return Store
     */
    private function getStore()
    {
        $store = Store::findOne($this->store_id);
        return $store;
    }

    public function saveData()
    {
        if (!$this->validate())
            return $this->getModelError();

        Option::set('user_center_data', $this->data, $this->store_id);
        return [
            'code' => 0,
            'msg' => '保存成功',
        ];
    }

    public function getData()
    {
        $store = $this->getStore();
        $data = Option::get('user_center_data', $this->store_id);
        $default_data = $this->getDefaultData();
        if (!$data) {
            $data = $default_data;
        } else {
            $data = json_decode($data, true);
        }
        if (!isset($data['copyright'])) {
            $data['copyright'] = [
                'text' => '',
                'icon' => '',
                'url' => '',
                'open_type' => '',
            ];
        }
        foreach ($data['menus'] as $i => $menu) {
            if ($menu['id'] == 'dianhua') {
                $data['menus'][$i]['tel'] = $store->contact_tel;
            }
        }
        return [
            'code' => 0,
            'data' => $data,
            'menu_list' => $default_data['menus'],
        ];
    }

    public function getDefaultData()
    {
        $store = $this->getStore();
        return [
            'user_center_bg' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/images/user-center/img-user-bg.png',
            'orders' => [
                'status_0' => [
                    'text' => '待付款',
                    'icon' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/images/user-center/icon-order-0.png',
                ],
                'status_1' => [
                    'text' => '待发货',
                    'icon' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/images/user-center/icon-order-1.png',
                ],
                'status_2' => [
                    'text' => '待收货',
                    'icon' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/images/user-center/icon-order-2.png',
                ],
                'status_3' => [
                    'text' => '已完成',
                    'icon' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/images/user-center/icon-order-3.png',
                ],
                'status_4' => [
                    'text' => '售后',
                    'icon' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/images/user-center/icon-order-4.png',
                ],
            ],
            'menus' => [
                [
                    'id' => 'pintuan',
                    'name' => '我的拼团',
                    'icon' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/images/user-center/icon-user-pt.png',
                    'open_type' => 'navigator',
                    'url' => '/pages/pt/order/order',
                    'tel' => '',
                ],
                [
                    'id' => 'yuyue',
                    'name' => '我的预约',
                    'icon' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/images/user-center/icon-about-us.png',
                    'open_type' => 'navigator',
                    'url' => '/pages/book/order/order',
                    'tel' => '',
                ],
                [
                    'id' => 'fenxiao',
                    'name' => '分销中心',
                    'icon' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/images/user-center/icon-user-fx.png',
                    'open_type' => 'navigator',
                    'url' => '/pages/share/index',
                    'tel' => '',
                ],
                [
                    'id' => 'kaquan',
                    'name' => '我的卡券',
                    'icon' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/images/user-center/icon-user-card.png',
                    'open_type' => 'navigator',
                    'url' => '/pages/card/card',
                    'tel' => '',
                ],
                [
                    'id' => 'youhuiquan',
                    'name' => '我的优惠券',
                    'icon' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/images/user-center/icon-user-yhq.png',
                    'open_type' => 'navigator',
                    'url' => '/pages/coupon/coupon',
                    'tel' => '',
                ],
                [
                    'id' => 'lingquan',
                    'name' => '领券中心',
                    'icon' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/images/user-center/icon-user-lingqu.png',
                    'open_type' => 'navigator',
                    'url' => '/pages/coupon-list/coupon-list',
                    'tel' => '',
                ],
                [
                    'id' => 'shoucang',
                    'name' => '我的收藏',
                    'icon' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/images/user-center/icon-user-sc.png',
                    'open_type' => 'navigator',
                    'url' => '/pages/favorite/favorite',
                    'tel' => '',
                ],
                [
                    'id' => 'kefu',
                    'name' => '在线客服',
                    'icon' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/images/user-center/icon-user-kf.png',
                    'open_type' => 'contact',
                    'url' => '',
                    'tel' => '',
                ],
                [
                    'id' => 'dianhua',
                    'name' => '联系我们',
                    'icon' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/images/user-center/icon-user-lx.png',
                    'open_type' => 'tel',
                    'url' => '',
                    'tel' => $store ? $store->contact_tel : '',
                ],
                [
                    'id' => 'fuwu',
                    'name' => '服务中心',
                    'icon' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/images/user-center/icon-help.png',
                    'open_type' => 'navigator',
                    'url' => '/pages/article-list/article-list?id=2',
                    'tel' => '',
                ],
                [
                    'id' => 'guanyu',
                    'name' => '关于我们',
                    'icon' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/images/user-center/icon-about-us.png',
                    'open_type' => 'navigator',
                    'url' => '/pages/article-detail/article-detail?id=about_us',
                    'tel' => '',
                ],
            ],
            'copyright' => [
                'text' => '',
                'icon' => '',
                'url' => '',
                'open_type' => '',
            ],
        ];
    }
}