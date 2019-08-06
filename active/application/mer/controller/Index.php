<?php
/**
 * Created by PhpStorm.
 * @User: 嘿嘿<skwordss@163.com>
 * Date: 2019/7/20 0020
 * Time: 19:25
 */
namespace app\mer\controller;



use library\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
        $this->title='商家后台管理';
        $this->menus = [
            [
                'id' => 1,
                'pid' => 0,
                'title' => '商家后台首页',
                'node' => "",
                'icon' => "",
                'url' => '/index.php/mer/index/main.html',
                'params' => '',
                'target' => '_self',
                'sort' => 500,
                'status' => 1,
                'create_at' => '2019-07-05 17:59:38',
            ],[
                'id' => 2,
                'pid' => 0,
                'title' => '活动管理',
                'node' => "",
                'icon' => "",
                'url' => '#',
                'params' => '',
                'target' => '_self',
                'sort' => 500,
                'status' => 1,
                'create_at' => '2019-07-05 17:59:38',
                'sub' => [
                    [
                        'id' => 3,
                        'pid' => 2,
                        'title' => '活动列表',
                        'node' => "",
                        'icon' => "layui-icon layui-icon-set",
                        'url' => '/index.php/mer/active_info/index',
                        'params' => '',
                        'target' => '_self',
                        'sort' => 0,
                        'status' => 1,
                        'create_at' => '2019-07-05 17:59:38',
                    ],[
                        'id' => 4,
                        'pid' => 2,
                        'title' => '订单列表',
                        'node' => "",
                        'icon' => "layui-icon layui-icon-set",
                        'url' => '#',
                        'params' => '',
                        'target' => '_self',
                        'sort' => 0,
                        'status' => 1,
                        'create_at' => '2019-07-05 17:59:38',
                    ],[
                        'id' => 5,
                        'pid' => 2,
                        'title' => '核销列表',
                        'node' => "",
                        'icon' => "layui-icon layui-icon-set",
                        'url' => '#',
                        'params' => '',
                        'target' => '_self',
                        'sort' => 0,
                        'status' => 1,
                        'create_at' => '2019-07-05 17:59:38',
                    ],
                ]
            ],[
                'id' => 6,
                'pid' => 0,
                'title' => '店铺设置',
                'node' => "",
                'icon' => "",
                'url' => '#',
                'params' => '',
                'target' => '_self',
                'sort' => 500,
                'status' => 1,
                'create_at' => '2019-07-05 17:59:38',
                'sub' => [
                    [
                        'id' => 3,
                        'pid' => 2,
                        'title' => '店铺状态',
                        'node' => "",
                        'icon' => "layui-icon layui-icon-set",
                        'url' => '/index.php/mer/goods_set/index',
                        'params' => '',
                        'target' => '_self',
                        'sort' => 0,
                        'status' => 1,
                        'create_at' => '2019-07-05 17:59:38',
                    ],[
                        'id' => 4,
                        'pid' => 2,
                        'title' => '店铺资料',
                        'node' => "",
                        'icon' => "layui-icon layui-icon-set",
                        'url' => '/index.php/mer/index/main.html',
                        'params' => '',
                        'target' => '_self',
                        'sort' => 0,
                        'status' => 1,
                        'create_at' => '2019-07-05 17:59:38',
                    ]
                ]
            ]
        ];
        if (empty(session('mer_user.id'))){
            $this->redirect('@mer/login');
        }
        $this->fetch();
    }

    /**
     * @name {statistical} 统计显示
     * @Date {2019/7/16 0016}
     * @author 嘿嘿 <skwordss@163.com>
     * @copyright v0.1
     * @since {2019/7/16 0016}-宋康-创建
     * @return array
     */
    private function statistical()
    {
        return [
            'goods' => Db::name('active_info')->where('m_id',session('mer_user.id'))->count(),
            //'order' => Db::name('yjl_order_mer')->where([['mer_id','=',session('mer_user.id')]])->count(),
            //'price' => Db::name('yjl_order_mer')->where([['is_state','in',[1,2]],['mer_id','=',session('mer_user.id')]])->sum('pay_total_fee'),
//            'collection' => Db::name('yjl_collection')->where([
//                ['to_id','=',session('mer_user.id')],
//                ['tags','=',1],
//                ['is_state','=',1]
//            ])->count()
        ];

    }

    /**
     * 后台环境信息
     */
    public function main()
    {
        $this->think_ver = \think\App::VERSION;
        $this->mysql_ver = Db::query('select version() as ver')[0]['ver'];
        $this->assign('statistical',$this->statistical());
        $this->fetch();
    }


}