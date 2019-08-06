<?php
/**
 * Created by PhpStorm.
 * @User: 嘿嘿<skwordss@163.com>
 * Date: 2019/7/21 0021
 * Time: 12:01
 */

namespace app\mer\controller;



use library\Controller;
use think\Db;

class Goods extends Controller
{
    protected $table = 'yjl_merchants_goods';

    public function index(){

        $this->title = '商品信息管理';
        $query = $this->_query($this->table)->where('attribution',session('mer_user.id'))->like('name')->equal('is_status,max_classify');
        $query->order('sales desc,id desc')
            ->withAttr('max_classify',function ($v){
                $className = Db::name('yjl_merchants_class')->where([
                    'id'=>$v,
                    'attribution'=>session('mer_user.id')
                ])->find();
                return $className['classification_name'];
            })
            ->json(['min_img', 'max_img', 'explain', 'attribute'])
            ->page();
    }

    /**
     * 数据列表处理
     * @param array $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function _index_page_filter(&$data)
    {
        $this->clist = Db::name('yjl_merchants_class')->where(['p_id' => 0, 'status' => '1','attribution'=>session('mer_user.id')])->select();
        $list = Db::name('yjl_merchants_goods')->where('is_status', '1')->whereIn('id', array_unique(array_column($data, 'id')))->json(['min_img', 'max_img', 'explain', 'attribute'])->select();
        foreach ($data as &$vo) {
            list($vo['list'], $vo['cate']) = [[], []];
            foreach ($list as $goods) if ($goods['id'] === $vo['id']) array_push($vo['list'], $goods);
            foreach ($this->clist as $cate) if ($cate['id'] === $vo['max_classify']) $vo['cate'] = $cate;
        }
    }

    /**
     * 添加商品信息
     */
    public function add()
    {
        if ($this->request->post('data')){
            $data = json_decode($this->request->post('data'),true);
            $type = $this->request->post('type');
            if ($type){
                foreach ($data as $v=>$k){
                    Db::name('yjl_merchants_class')->where([
                        'source_id' => $k['value'],
                        'attribution' => session('mer_user.id')
                    ])->delete();
                    Db::name('yjl_merchants_goods')->where([
                        'max_classify' => $k['value'],
                        'attribution' => session('mer_user.id')
                    ])->delete();
                }
                $this->success('移除成功');
            }else{
                foreach ($data as $v=>$k){
                   $ids = Db::name('yjl_merchants_class')->insertGetId([
                        'classification_name' => $k['title'],
                        'source_id' => $k['value'],
                        'p_id' =>0,
                        'attribution' => session('mer_user.id'),
                        'status' => 1
                    ]);
                    $goodsInfo = Db::name('yjl_goods')->where('max_classify',$k['value'])->select();
                    foreach ($goodsInfo as $value=>$key){
                        Db::name('yjl_merchants_goods')->insert(
                            [
                                'goods_id' => $key['id'],
                                'name' => $key['name'],
                                'desc' => $key['desc'],
                                'explain' => $key['explain'],
                                'attribute' => $key['attribute'],
                                'inventory' => $key['inventory'],
                                'max_classify' => $ids,
                                'min_classify' => $key['min_classify'],
                                'min_img' => $key['min_img'],
                                'max_img' => $key['max_img'],
                                'sales' => $key['sales'],
                                'price' => $key['price'],
                                'attribution' => session('mer_user.id'),
                                'store_id' => session('mer_user.id'),
                                'create_time' => $key['create_time'],
                            ]
                        );
                    }
                }
                $this->success('导入成功');
            }
        }
        $myInfo = Db::name('yjl_merchants_class')->where('attribution',session('mer_user.id'))->select();
        $myClass = [];
        foreach ($myInfo as $v=>$k){
            $myClass[] = $k['source_id'];
        }
        $toInfo = Db::name('yjl_classification')->where(['p_id'=>0,'status'=>1])->select();
        $toClass = [];
        foreach ($toInfo as $v=>$k){
            $toClass[] = [
                'value'=>"{$k['id']}",
                'title'=>str_replace(' ', '', $k['classification_name'])
            ];
        }
        $this->assign('data1',json_encode($toClass,JSON_UNESCAPED_UNICODE));
        $this->assign('data2',json_encode($myClass));
        $this->fetch();

    }

    /**
     * 禁用商品信息
     */
    public function forbid()
    {
        $this->_save($this->table, ['is_status' => '0']);
    }

    /**
     * 启用商品信息
     */
    public function resume()
    {
        $this->_save($this->table, ['is_status' => '1']);
    }

    /**
     * 删除商品信息
     */
    public function remove()
    {
        $this->_delete($this->table);
    }
}