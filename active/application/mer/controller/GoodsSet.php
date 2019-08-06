<?php
/**
 * Created by PhpStorm.
 * @User: 嘿嘿<skwordss@163.com>
 * Date: 2019/7/21 0021
 * Time: 17:25
 */

namespace app\mer\controller;

use library\Controller;
use think\Db;

class GoodsSet extends Controller
{



    public function index()
    {
        $this->assign('info',Db::name('active_mer')->where('id',session('mer_user.id'))->find());
        $this->fetch();
    }
}