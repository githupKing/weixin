<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2019 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://demo.thinkadmin.top
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee 代码仓库：https://gitee.com/zoujingli/ThinkAdmin
// | github 代码仓库：https://github.com/zoujingli/ThinkAdmin
// +----------------------------------------------------------------------

namespace app\mer\controller;

use library\Controller;

/**
 * 商品分类管理
 * Class GoodsCate
 * @package app\store\controller
 */
class GoodsCate extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    protected $table = 'yjl_merchants_class';

    public function index()
    {
        $this->title = '商品分类管理';
        $query = $this->_query($this->table)->like('classification_name')->equal('status');
        $query->where(['attribution' => session('mer_user.id')])->order('sort desc,id desc')->page();
    }

    /**
     * 添加商品分类
     */
    public function add()
    {
        $this->title = '添加商品分类';
        $this->_form($this->table, 'form');
    }

    /**
     * 编辑商品分类
     */
    public function edit()
    {
        $this->title = '编辑商品分类';
        $this->_form($this->table, 'form');
    }

    /**
     * 禁用商品分类
     */
    public function forbid()
    {
        $this->_save($this->table, ['status' => '0']);
    }

    /**
     * 启用商品分类
     */
    public function resume()
    {
        $this->_save($this->table, ['status' => '1']);
    }

    /**
     * 删除商品分类
     */
    public function remove()
    {
        $this->_delete($this->table);
    }

}
