<?php
/**
 * Created by PhpStorm.
 * @User: 嘿嘿<skwordss@163.com>
 * Date: 2019/7/22 0022
 * Time: 16:07
 */
namespace app\mer\controller;

use library\Controller;
use function Qiniu\waterText;
use think\Db;

/**
 * 活动预览
 * Class Goods
 * @package app\store\controller
 */
class Active extends Controller
{
    /**
     * 指定数据表
     * @var string
     */
    protected $table = 'ActiveInfo';


    public function index()
    {
        //活动主体
        $info = Db::name($this->table)->where('id',$this->request->param('id'))->find();
        //活动红包
        $packet = Db::name('active_packet')->where('a_id',$this->request->param('id'))->find();
        //商家信息
        $mer = Db::name('active_mer')->where('id',$info['m_id'])->find();
        //活动规则
        $vouchers = Db::name('active_vouchers')->where('a_id',$this->request->param('id'))->select();
        //红包排行
        $share = Db::name('active_share')->where('a_id',$this->request->param('id'))->order('packet_price DESC')->select();
        $this->assign([
            'info' => $info,
            'packet' => $packet,
            'mer' => $mer,
            'vouchers' => $vouchers,
            'share' => $share,
            'qualifying' => 999,
            'qualifying_price' => 0.00
        ]);
        if ($info['active_tags']){
            $this->fetch('shopActive');
        }else{
            $this->fetch('index2');
        }
    }
}