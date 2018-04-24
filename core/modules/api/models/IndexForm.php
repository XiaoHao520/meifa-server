<?php
/**
 * Created by IntelliJ IDEA.
 * User: luwei
 * Date: 2017/7/5
 * Time: 16:00
 */

namespace app\modules\api\models;


use app\models\Banner;
use app\models\Cat;
use app\models\Coupon;
use app\models\FxhbHongbao;
use app\models\FxhbSetting;
use app\models\Goods;
use app\models\GoodsPic;
use app\models\HomeBlock;
use app\models\HomeNav;
use app\models\HomePageModule;
use app\models\MiaoshaGoods;
use app\models\Option;
use app\models\PtGoods;
use app\models\PtOrder;
use app\models\PtOrderDetail;
use app\models\Store;
use app\models\Topic;
use app\models\User;
use app\models\UserCoupon;
use app\models\YyGoods;
use app\modules\mch\models\book\YyGoodsForm;
use yii\data\Sort;
use yii\helpers\VarDumper;

class IndexForm extends Model
{
    public $store_id;

    public function search()
    {
        $store = Store::findOne($this->store_id);
        if (!$store)
            return [
                'code' => 1,
                'msg' => 'Store不存在',
            ];

        $this->getMiaoshaData();
        $banner_list = Banner::find()->where([
            'is_delete' => 0,
            'store_id' => $this->store_id,
            'type' => 1,
        ])->orderBy('sort ASC')->asArray()->all();
        foreach ($banner_list as $i => $banner) {
            $banner_list[$i]['open_type'] = 'navigate';
        }

        $nav_icon_list = HomeNav::find()->where([
            'is_delete' => 0,
            'store_id' => $this->store_id,
        ])->orderBy('sort ASC,addtime DESC')->select('name,pic_url,url,name,open_type')->asArray()->all();

        $cat_list = Cat::find()->where([
            'is_delete' => 0,
            'parent_id' => 0,
            'store_id' => $this->store_id,
        ])->orderBy('sort ASC')->asArray()->all();
        foreach ($cat_list as $i => $cat) {
            $cat_list[$i]['page_url'] = '/pages/list/list?cat_id=' . $cat['id'];
            $cat_list[$i]['open_type'] = 'navigate';
            $cat_list[$i]['cat_pic'] = $cat['pic_url'];
            $goods_list_form = new GoodsListForm();
            $goods_list_form->store_id = $this->store_id;
            $goods_list_form->cat_id = $cat['id'];
            $goods_list_form->limit = $store->cat_goods_count;
            $goods_list_form_res = $goods_list_form->search();
            $goods_list = $goods_list_form_res['code'] == 0 ? $goods_list_form_res['data']['list'] : [];
            $cat_list[$i]['goods_list'] = $goods_list;
        }

        $block_list = HomeBlock::find()->where(['store_id' => $this->store_id, 'is_delete' => 0])->all();
        $new_block_list = [];
        foreach ($block_list as $item) {
            $new_block_list[] = [
                'id' => $item->id,
                'name' => $item->name,
                'data' => json_decode($item->data, true),
            ];
        }
        $user_id = \Yii::$app->user->identity->id;
        $coupon_form = new CouponListForm();
        $coupon_form->store_id = $this->store_id;
        $coupon_form->user_id = $user_id;
        $coupon_list = $coupon_form->getList();

        $topic_list = Topic::find()->where(['store_id' => $this->store_id, 'is_delete' => 0])->orderBy('sort ASC,addtime DESC')->limit(6)->select('id,title')->asArray()->all();
        $option = Option::getList('service,web_service,web_service_url', $this->store_id, 'admin', '');

        $update_list = Option::get('home_page_data', $this->store_id, 'app');
        if (!$update_list) {
            $update_form = new HomePageModule();
            $update_list = $update_form->getDefaultData();
        } else {
            $update_list = json_decode($update_list, true);
        }
     /*
      *  人气发型师
      * */


     //$sort=new Sort(['attributes'=>]);


    $yy_goods= YyGoods::find()->where(['store_id'=>$this->store_id,'is_delete'=>0])->orderBy(['sales'=>'desc'])->limit(5)->asArray()->all();














        return [
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'module_list' => $this->getModuleList($store),
                'store' => [
                    'id' => $store->id,
                    'name' => $store->name,
                    'is_coupon' => $store->is_coupon,
                    'show_customer_service' => $store->show_customer_service,
                    'dial' => $store->dial,
                    'dial_pic' => $store->dial_pic,
                    'service' => $option['service'],
                    'copyright' => $store->copyright,
                    'copyright_pic_url' => $store->copyright_pic_url,
                    'copyright_url' => $store->copyright_url,
                    'contact_tel' => $store->contact_tel,
                    'cat_style' => $store->cat_style,
                    'cut_thread' => $store->cut_thread,
                    'address' => $store->address,
                    'is_offline' => $store->is_offline,
                    'option' => $option,
                ],
                'banner_list' => $banner_list,
                'nav_icon_list' => $nav_icon_list,
                'cat_goods_cols' => $store->cat_goods_cols,
                'cat_list' => $cat_list,
                'block_list' => $new_block_list,
                'coupon_list' => $coupon_list,
                'topic_list' => $topic_list,
                'nav_count' => $store->nav_count,
                'notice' => Option::get('notice', $this->store_id, 'admin'),
                'miaosha' => $this->getMiaoshaData(),
                'pintuan' => $this->getPintuanData(),
                'update_list' => $update_list,
                'act_modal_list' => $this->getActModalList(),
                'yy_goods'=>$yy_goods
            ],
        ];
    }

    private function getBlockList()
    {

    }

    /**
     * @param Store $store
     */
    private function getModuleList($store)
    {
        $list = json_decode($store->home_page_module, true);
        if (!$list) {
            $list = [
                [
                    'name' => 'banner',
                ],
                [
                    'name' => 'search',
                ],
                [
                    'name' => 'nav',
                ],
                [
                    'name' => 'topic',
                ],
                [
                    'name' => 'coupon',
                ],
                [
                    'name' => 'cat',
                ],
            ];
        } else {
            $new_list = [];
            foreach ($list as $item) {
                if (stripos($item['name'], 'block-') !== false) {
                    $names = explode('-', $item['name']);
                    $new_list[] = [
                        'name' => $names[0],
                        'block_id' => $names[1],
                    ];
                } elseif (stripos($item['name'], 'single_cat-') !== false) {
                    $names = explode('-', $item['name']);
                    $new_list[] = [
                        'name' => $names[0],
                        'cat_id' => $names[1],
                    ];
                } else {
                    $new_list[] = $item;
                }
            }
            $list = $new_list;
        }
        return $list;
    }

    public function getMiaoshaData()
    {
        $list = MiaoshaGoods::find()->alias('mg')
            ->select('g.id,g.name,g.cover_pic AS pic,g.price,mg.attr,mg.start_time')
            ->leftJoin(['g' => Goods::tableName()], 'mg.goods_id=g.id')
            ->where([
                'AND',
                [
                    'mg.is_delete' => 0,
                    'g.is_delete' => 0,
                    'mg.open_date' => date('Y-m-d'),
                    'g.status' => 1,
                    'mg.start_time' => date('H'),
                    'mg.store_id' => $this->store_id,
                ],
            ])
            ->orderBy('g.sort ASC,g.addtime DESC')
            ->limit(10)
            ->asArray()->all();
        foreach ($list as $i => $item) {
            $item['attr'] = json_decode($item['attr'], true);
            $list[$i] = $item;
            $price_list = [];
            foreach ($item['attr'] as $attr) {
                if ($attr['miaosha_price'] <= 0) {
                    $price_list[] = doubleval($item['price']);
                } else {
                    $price_list[] = doubleval($attr['miaosha_price']);
                }
            }
            $list[$i]['price'] = number_format($list[$i]['price'], 2, '.', '');
            $list[$i]['miaosha_price'] = number_format(min($price_list), 2, '.', '');
            unset($list[$i]['attr']);
        }
        if (count($list) == 0)
            return [
                'name' => '暂无秒杀活动',
                'rest_time' => 0,
                'goods_list' => null,
            ];
        return [
            'name' => intval(date('H')) . '点场',
            'rest_time' => max(intval(strtotime(date('Y-m-d H:59:59')) - time()), 0),
            'goods_list' => $list,
        ];
    }

    public function getPintuanData()
    {
        $num_query = PtOrderDetail::find()->alias('pod')
            ->select('pod.goods_id,SUM(pod.num) AS sale_num')
            ->leftJoin(['po' => PtOrder::tableName()], 'pod.order_id=po.id')
            ->where([
                'AND',
                [
                    'pod.is_delete' => 0,
                    'po.is_delete' => 0,
                    'po.is_pay' => 1,
                ],
            ])->groupBy('pod.goods_id');
        $list = PtGoods::find()->alias('pg')
            ->select('pg.*,pod.sale_num')
            ->leftJoin(['pod' => $num_query], 'pg.id=pod.goods_id')
            ->where([
                'AND',
                [
                    'pg.is_delete' => 0,
                    'pg.status' => 1,
                    'pg.store_id' => $this->store_id,
                ],
            ])->orderBy('pg.is_hot DESC,pg.sort ASC,pg.addtime DESC')
            ->limit(10)
            ->asArray()->all();
        $new_list = [];
        foreach ($list as $item) {
            $new_list[] = [
                'id' => $item['id'],
                'pic' => $item['cover_pic'],
                'name' => $item['name'],
                'price' => number_format($item['price'], 2, '.', ''),
                'sale_num' => intval($item['sale_num'] ? $item['sale_num'] : 0) + intval($item['virtual_sales'] ? $item['virtual_sales'] : 0),
            ];
        }
        return [
            'goods_list' => $new_list,
        ];
    }

    /**
     * 获取首页活动弹窗列表
     */
    public function getActModalList()
    {
        $act_list = [];
        $fxhb_act = $this->getFxhbAct();
        if ($fxhb_act) {
            $act_list[] = $fxhb_act;
        }
        foreach ($act_list as $i => $item) {
            if ($i == 0)
                $act_list[$i]['show'] = true;
            else
                $act_list[$i]['show'] = false;
        }
        return $act_list;
    }

    private function getFxhbAct()
    {
        $act_data = [
            'name' => '一起拆红包',
            'pic_url' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/images/fxhb/act_modal.png',
            'pic_width' => 750,
            'pic_height' => 696,
            'url' => '/pages/fxhb/open/open',
            'open_type' => 'navigate',
        ];
        $fxhb_setting = FxhbSetting::findOne([
            'store_id' => $this->store_id,
        ]);
        if (!$fxhb_setting || $fxhb_setting->game_open != 1) {
            return null;
        }
        if ($user = \Yii::$app->user->isGuest)
            return null;
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        /** @var FxhbHongbao $hongbao */
        $hongbao = FxhbHongbao::find()->where([
            'user_id' => $user->id,
            'store_id' => $this->store_id,
            'parent_id' => 0,
            'is_finish' => 0,
            'is_expire' => 0,
        ])->one();
        if (!$hongbao)
            return $act_data;
        if (time() > $hongbao->expire_time) {
            $hongbao->is_expire = 1;
            $hongbao->save();
            return $act_data;
        }
        return null;
    }
}