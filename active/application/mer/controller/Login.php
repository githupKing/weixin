<?php
/**
 * Created by PhpStorm.
 * @User: 嘿嘿<skwordss@163.com>
 * Date: 2019/7/20 0020
 * Time: 20:01
 */

namespace app\mer\controller;

use library\Controller;
use think\Db;

class Login extends Controller
{
    public function index()
    {
        $this->title = '商家登录入口';
        if ($this->request->isGet()){
            if (session('mer_user.id')){
                $this->redirect('@mer');
            }else{
                $this->loginskey = session('loginskey');
                if (empty($this->loginskey)) {
                    $this->loginskey = uniqid();
                    session('loginskey', $this->loginskey);
                }
                $this->fetch();
            }
        }else{
            $info = Db::name('active_mer')->where('mer_account',$this->request->post('username'))->find();
            if (empty($info)){
                $this->error("<strong class='color-red'>抱歉，该账号不存在！</strong>");
            }else{
                session('loginskey', null);
                session('mer_user_id',$info['id']);
                if (empty($info['merchants_pwd'])){
                    $this->success('验证成功',url('@mer/login/set'));
                }else{
                    $this->success('验证成功',url('@mer/login/login'));
                }
            }

        }
    }

    public function set()
    {
        $this->title = '设置密码';
        if ($this->request->isGet()){
            if (!session('mer_user_id')){
                $this->redirect('@mer');
            }else{
                $this->fetch();
            }
        }else{
            $data = $this->_input([
                'tel' => $this->request->post('tel'),
                'password' => $this->request->post('password'),
            ], [
                'tel' => 'require|min:11',
                'password' => 'require|min:4',
            ], [
                'tel.require' => '手机号不能为空！',
                'password.require' => '登录密码不能为空！',
            ]);
            $map = ['id'=>session('mer_user_id'),'mer_tel'=>$data['tel']];
            $info = Db::name('active_mer')->where($map)->find();
            if (empty($info)){
                $this->error("<strong class='color-red'>抱歉，手机验证失败！</strong>");
            }else{
                $updateUser = Db::name('active_mer')->where('id',$info['id'])->update([
                    'mer_pwd' => md5($data['password'])
                ]);
                if ($updateUser){
                    session('mer_user_id',NULL);
                    session('mer_user',$info);
                    $this->success('登录成功',url('@mer'));
                }else{
                    $this->error("<strong class='color-red'>抱歉，密码设置失败！</strong>");
                }
            }

        }
    }

    public function login()
    {
        $this->title = '验证密码';
        if ($this->request->isGet()){
            if (!session('mer_user_id')){
                $this->redirect('@mer');
            }else{
                $this->fetch();
            }
        }else{
            $data = $this->_input([
                'password' => $this->request->post('password'),
            ], [
                'password' => 'require|min:4',
            ], [
                'password.require' => '登录密码不能为空！',
            ]);
            $map = ['id'=>session('mer_user_id'),'mer_pwd'=>md5($data['password'])];
            $info = Db::name('yjl_merchants')->where($map)->find();
            if (empty($info)){
                $this->error("<strong class='color-red'>抱歉，密码验证失败！</strong>");
            }else{
                session('mer_user_id',NULL);
                session('mer_user',$info);
                $this->success('登录成功',url('@mer'));
            }

        }
    }

    public function out()
    {
        \think\facade\Session::clear();
        \think\facade\Session::destroy();
        $this->success('退出登录成功！', url('@mer/login'));
    }
}