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
class Index extends Controller
{
    /**
     * 指定数据表
     * @var string
     */
    protected $table = 'ActiveInfo';

    /**
     * 活动信息管理
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
        $this->title = '活动信息管理';
        $query = $this->_query($this->table)->equal('is_status,is_active')->like('active_name,m_name');
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
        $this->mer = Db::name('active_mer')->where([
            'is_status'=>1,
            'is_permissions'=>1
        ])->select();
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
        $this->mer = Db::name('active_mer')->where([
            'is_status'=>1,
            'is_permissions'=>1
        ])->select();
        $this->_form($this->table, 'form');
    }

    /**
     * 编辑提交
     */
    public function editPost()
    {
        if ($this->request->isPost()){
            if (!empty($this->request->param()['active_music_switch']) && empty($this->request->param()['active_bg_music'])){
                $this->error('请上传背景音乐');
            }
            if (empty($this->request->param()['active_bg_images'])){
                $this->error('请上传活动背景图');
            }
            if (empty($this->request->param()['active_name'])){
                $this->error('请输入活动名称');
            }
            if (empty($this->request->param()['start_time'])){
                $this->error('请输入活动开始时间');
            }
            if (empty($this->request->param()['stop_time'])){
                $this->error('请输入活动结束时间');
            }
            if (!empty($this->request->param()['active_pack_switch'])){
                if (empty($this->request->param()['pack_money'])
                    ||
                    empty($this->request->param()['pack_share_money'])
                    ||
                    empty($this->request->param()['pack_share_moneyx']))
                {
                    $this->error('请设置活动红包');
                }
            }
            if (
                !empty($this->request->param()['active_business_switch'])

            ){
                if ( empty($this->request->param()['business_images'])
                    ||
                    empty($this->request->param()['business_bg_images'])
                    ||
                    empty($this->request->param()['business_phone'])){
                    $this->error('请设置商务合作信息');
                }

            }
            switch ($this->request->param()['active_tags']){
                case 1:
                    if (
                        empty($this->request->param()['m_id'])
                        ||
                        empty($this->request->param()['vouchers'])

                    ){
                        $this->error('请设置优惠券信息');
                    }
                    break;
                case 2:
                    if (
                        empty($this->request->param()['apply'])
                        ||
                        empty($this->request->param()['apply_price'])
                        ||
                        empty($this->request->param()['apply_sum'])
                    ){
                        $this->error('请设置报名信息');
                    }
                    break;
            }
            if (empty($this->request->param()['active_rules'])){
                $this->error('请设置活动规则');
            }
            $data = $this->request->param();
            !empty($this->request->param()['vouchers'])?$data['vouchers'] = json_encode($this->request->param()['vouchers']):"";
            !empty($this->request->param()['apply'])?$data['apply'] = json_encode($this->request->param()['apply'] ):"";

            if (Db::name($this->table)->insert($data)){
                $this->success('保存成功');
            }else{
                $this->error('保存失败');
            }
        }else{
            $this->error('保存失败');
        }

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
        $this->_save($this->table, ['is_status' => '1']);
    }


    /**
     * 取消审核
     * @auth true
     */
    public function Noaudit()
    {
        $this->_save($this->table, ['is_status' => '0']);
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