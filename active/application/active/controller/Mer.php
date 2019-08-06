<?php
/**
 * Created by PhpStorm.
 * @User: 嘿嘿<skwordss@163.com>
 * Date: 2019/7/22 0022
 * Time: 11:30
 */

namespace app\active\controller;

use library\Controller;
use think\Db;

/**
 * 活动管理
 * Class Goods
 * @package app\store\controller
 */
class Mer extends Controller
{
    /**
     * 指定数据表
     * @var string
     */
    protected $table = 'ActiveMer';

    /**
     * 商家信息管理
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function index()
    {
        $this->title = '商家信息管理';
        $query = $this->_query($this->table)->equal('is_status,is_permissions')->like('mer_name,mer_account');
        $query->order('id desc')->page();
    }

    /**
     * 添加商品信息
     * @auth true
     */
    public function add()
    {
        $this->title = '添加商品信息';
        $this->isAddMode = '1';
        $this->_form($this->table, 'form');
    }

    /**
     * 编辑商品信息
     * @auth true
     */
    public function edit()
    {
        $this->title = '编辑商品信息';
        $this->isAddMode = '0';
        $this->_form($this->table, 'form');
    }

    /**
     * 查看红包信息
     * @auth true
     */
    public function selectRedPacket()
    {
        $this->title = '查看红包信息';
        if ($this->request->isGet()){
            $info = Db::name('active_packet')->where('a_id',$this->request->param('id'))->find();
            $this->assign('a_id',$this->request->param('id'));
            $ids = $this->request->param('id');
        }else{
            $info = Db::name('active_packet')->where('a_id',$this->request->param('a_id'))->find();
            $this->assign('a_id',$this->request->param('a_id'));
            $ids = $this->request->param('a_id');
        }
        $this->assign('vo',$info);
        if (empty($info)){
            $this->isAddMode = '1';
            $this->_form('active_packet','packet','',['a_id'=>$ids]);
        }else{
            $this->isAddMode = '0';
            $this->_form('active_packet' ,'packet','',['a_id'=>$ids]);
        }

    }

    /**
     * 查看券信息
     */
    public function vouchers()
    {
        $this->title = '查看券信息';
        if ($this->request->isGet()){
            $info = Db::name('active_vouchers')->where('a_id',$this->request->param('id'))->find();
            $this->assign('a_id',$this->request->param('id'));
            $ids = $this->request->param('id');
        }else{
            $info = Db::name('active_vouchers')->where('a_id',$this->request->param('a_id'))->find();
            $this->assign('a_id',$this->request->param('a_id'));
            $ids = $this->request->param('a_id');
        }
        $this->assign('vo',$info);
        if (empty($info)){
            $this->isAddMode = '1';
            $this->_form('ActiveVouchers','vouchers','',['a_id'=>$ids]);
        }else{
            $this->isAddMode = '0';
            $this->_form('ActiveVouchers' ,'vouchers','',['a_id'=>$ids]);
        }

    }

    /**
     * 通过审核
     * @auth true
     */
    public function okAudit()
    {
        $this->_save($this->table, ['is_permissions' => '1']);
    }


    /**
     * 取消审核
     * @auth true
     */
    public function Noaudit()
    {
        $this->_save($this->table, ['is_permissions' => '0']);
    }

    /**
     * 删除活动信息
     * @auth true
     */
    public function remove()
    {
        $this->_delete($this->table);
    }

}