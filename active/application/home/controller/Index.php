<?php
/**
 * Created by PhpStorm.
 * @User: 嘿嘿<skwordss@163.com>
 * Date: 2019/7/24 0024
 * Time: 10:58
 */

namespace  app\home\controller;

use app\home\model\ActiveInfo;
use app\wechat\service\WechatService;
use library\Controller;
use think\Db;
use think\Request;

class Index extends Controller
{
    /**
     * @var array $active 获得信息
     */
    private $active;

    /**
     * @name {index} 获得首页（引导关注）
     * @Date {2019/7/25 0025}
     * @author 嘿嘿 <skwordss@163.com>
     * @copyright v0.1
     * @since {2019/7/25 0025}-宋康-创建
     */
    public function index()
    {
        if ($this->request->isGet()){
            $data = $this->request->param();
            $this->assign('qrcode',$this->showQrc(0));
            $this->assign('packetCode',$this->showQrc(0,$this->request->domain()."/index.php/home/index/packet"));
            if (!empty($data)){
                $this->authWechat($this->request->url(true));
                $this->assign('jsSdk',$this->jsSdk($this->request->url(true)));
                $to_id = '';
                if (isset($_GET['to_id'])){
                    $to_id = $_GET['to_id'];
                }
                $this->assign('to_id',$to_id);
                Db::name('active_info')->where('id',$_GET['a_id'])->setInc('active_preview',1);
                if (session('user_info.subscribe')){
                    $this->assign('postersCode',$this->showQrc(0,$this->request->domain()."/index.php/home/index/index?a_id={$_GET['a_id']}&to_id=".session('user_info.id').""));
                    //已关注
                    //$this->assign('actInfo',$this->getActive($_GET['a_id']));

                    $active = $this->getActive($_GET['a_id']);

                    if (!empty($active[0])){
                        if (isset($_GET['to_id']) && $_GET['to_id'] !== session('user_info.id')){

                        }
                        $this->assign('actInfo',$active[0]);
                        $this->assign('mer',$active[1]);
                        if (time() > strtotime($active[0]['stop_time'])){
                            $this->fetch('end');
                        }
                        $this->assign('share',[]);
                        $this->fetch('index');
                    }else{
                        $this->fetch('focus');
                    }

                }else{
                    //未关注
                    if (isset($_GET['to_id']) && isset($_GET['a_id'])){
                        if (!empty($toUser = $this->getToUser($_GET['to_id']))){
                            //有邀请人ID
                            $share = Db::name('active_wechat_share')->where([
                                'u_id' => session('user_info.id'),
                                'to_id' => $_GET['to_id'],
                                'a_id' => $_GET['a_id']
                            ])->find();
                            if (empty($share)){
                                Db::name('active_wechat_share')->insert([
                                    'u_id' => session('user_info.id'),
                                    'u_name' => session('user_info.fansinfo.nickname'),
                                    'u_headimg' => session('user_info.fansinfo.headimgurl'),
                                    'to_id' => $toUser['id'],
                                    'to_name' => $toUser['nickname'],
                                    'to_headimg' => $toUser['headimgurl'],
                                    'share_ct' => date("Y-m-d H:i:s"),
                                    'a_id' => $_GET['a_id']
                                ]);
                            }
                        }
                    }
                    $this->fetch('focus');
                }
            }else{
                // 未绑定活动
                $this->fetch('focus');
            }
        }
    }


    /**
     * @name {preview} 活动预览
     * @Date {2019/8/6 0006}
     * @author 嘿嘿 <skwordss@163.com>
     * @copyright v0.1
     * @since {2019/8/6 0006}-宋康-创建
     * @return mixed
     */
    public function preview()
    {
        if ($this->request->isGet()){
            if ($_GET['a_id']){
                $active = $this->getActive($_GET['a_id']);
                $this->assign('actInfo',$active[0]);
                $this->assign('mer',$active[1]);
                $this->assign('share',[]);
                $this->fetch();
            }else{
                echo "该活动不存在";
            }
        }
    }

    /**
     * @name {myInfo} 个人中心
     * @Date {2019/8/6 0006}
     * @author 嘿嘿 <skwordss@163.com>
     * @copyright v0.1
     * @since {2019/8/6 0006}-宋康-创建
     * @return mixed
     */
    public function myInfo()
    {
        $info = session('user_info');
        if (empty($info)){
            $this->authWechat($this->request->url(true));
        }else{
            $vouchers = [
                'unused' =>[
                    [
                        'id'=>1,
                        'price'=>0.01,
                        'worth' => 120,
                        'name' => '抵扣券',
                        'stop_time' => '2019-10-10'
                    ],
                    [
                        'id'=>2,
                        'price'=>0.01,
                        'worth' => 120,
                        'name' => '满80元可用',
                        'stop_time' => '2019-10-10'
                    ]
                ],
                'have' => [],
                'expired' => []
            ];
            $this->assign('vouchers',$vouchers);
            $this->assign('info',$info);
            $this->fetch();
        }
    }

    /**
     * @name {coupons}
     * @Date {2019/8/6 0006}
     * @author 嘿嘿 <skwordss@163.com>
     * @copyright v0.1
     * @since {2019/8/6 0006}-宋康-创建
     * @return mixed
     */
    public function coupons()
    {
        $this->fetch();
    }

    /**
     * @name {signUp} 报名活动
     * @Date {2019/8/6 0006}
     * @author 嘿嘿 <skwordss@163.com>
     * @copyright v0.1
     * @since {2019/8/6 0006}-宋康-创建
     * @return mixed
     */
    public function signUp()
    {
        $this->fetch();
    }

    /**
     * @name {reflect} 立即提现
     * @Date {2019/8/6 0006}
     * @author 嘿嘿 <skwordss@163.com>
     * @copyright v0.1
     * @since {2019/8/6 0006}-宋康-创建
     * @return mixed
     */
    public function reflect()
    {
        $this->fetch();
    }


    /**
     * @name {shareSuccess}
     * @Date {2019/8/10 0010}
     * @author 嘿嘿 <skwordss@163.com>
     * @copyright v0.1
     * @since {2019/8/10 0010}-宋康-创建
     * @return object
     */
    public function shareSuccess()
    {
        if ($this->request->isAjax()){
            $active = $this->getActive($this->request->param('id'));
            if (!empty($active)){
               if ($active['active_pack_switch'] == 'on'){
                   //开启红包
                   $pack = Db::name('active_packet')
                       ->where([
                           'a_id' => $this->request->param('id'),
                           'u_id' => session('user_info.id')
                       ])
                       ->find();
                   if (empty($pack)){
                       //首次分享
                       switch ($active['pack_share']){
                           case 0:
                             // Db::name()->insert();
                               break;
                           case 1:

                               break;
                       }
                   }else{
                       //好友分享
                       if (!empty($this->request->param('to_id'))){

                       }else{

                       }
                   }
               }
               if ($active['active_distribution'] == 'on' && $active['active_tags'] !== 1){
                   //开启分销
               }
            }
            return json(['code'=>1,'msg'=>'抱歉，活动以关闭或者不存在']);
        }
    }

    /**
     * @name {packet} 红包界面
     * @Date {2019/7/25 0025}
     * @author 嘿嘿 <skwordss@163.com>
     * @copyright v0.1
     * @since {2019/7/25 0025}-宋康-创建
     */
    public function packet()
    {
        $this->assign('jsSdk',$this->jsSdk($this->request->url(true)));
        $this->fetch('envelope');
    }


    /**
     * @param $url
     * @name {authWechat} 获取微信用户信息
     * @Date {2019/7/24 0024}
     * @author 嘿嘿 <skwordss@163.com>
     * @copyright v0.1
     * @since {2019/7/24 0024}-宋康-创建
     */
    public function authWechat($url)
    {
        $this->fans = WechatService::getWebOauthInfo($url);
        if (!empty($this->fans)){
            $fans = Db::name('wechat_fans')->where([
                'openid'=> $this->fans['openid']
            ])->find();
            if (empty($this->fans['fansinfo'])){
                $this->fans['fansinfo'] = $fans;
                $this->fans['subscribe'] = $fans['subscribe'];
                $this->fans['id'] = $fans['id'];
            }else{
                $this->fans['fansinfo'] = $fans;
                $this->fans['id'] = $fans['id'];
                switch ($fans['subscribe']){
                    case 0:
                        $this->fans['subscribe'] = 0;
                        break;
                    case 1:
                        $this->fans['subscribe'] = 1;
                        break;
                }
            }
            session('user_info',$this->fans);
        }
    }


    /**
     * 创建二维码响应对应
     * @param string $url 二维码内容
     * @throws \Endroid\QrCode\Exceptions\ImageFunctionFailedException
     * @throws \Endroid\QrCode\Exceptions\ImageFunctionUnknownException
     * @throws \Endroid\QrCode\Exceptions\ImageTypeInvalidException
     */
    protected function showQrc($tags=0,$url = 'https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=Mzg3MzI4ODQwOQ==#wechat_redirect')
    {
        $qrCode = new \Endroid\QrCode\QrCode();
        $qrCode->setText($url)->setSize(300)->setPadding(20)->setImageType('png');
        switch ($tags){
            case 0:
                return base64_encode($qrCode->get());
                break;
            case 1:
                response($qrCode->get(), 200, ['Content-Type' => 'image/png'])->send();
                break;
        }
    }

    /**
     * @param $id
     * @name {getToUser}
     * @Date {2019/7/25 0025}
     * @author 嘿嘿 <skwordss@163.com>
     * @copyright v0.1
     * @since {2019/7/25 0025}-宋康-创建
     * @return array
     */
    protected function getToUser($id)
    {
       return Db::name('wechat_fans')->where('id',$id)->find();
    }

    /**
     * @param $id
     * @name {getActive}
     * @Date {2019/7/25 0025}
     * @author 嘿嘿 <skwordss@163.com>
     * @copyright v0.1
     * @since {2019/7/25 0025}-宋康-创建
     */
    protected function getActive($id)
    {
        $model = new ActiveInfo();
        $active = $model->where('id',$id)->find();
        if (!empty($active['vouchers'])){
            $active['vouchers'] = json_decode($active['vouchers'],true);
        }
        if (!empty($active['apply'])){
            $active['apply'] = json_decode($active['apply'],true);
        }
        $mer = [];
        switch ($active['active_tags']){
            case 0:
                if ($active['m_id']){
                    $mer = Db::name('active_mer')->where('id',$active['m_id'])->select();
                }
                break;
            case 1:
                if ($active['m_id']){
                    $mer = Db::name('active_mer')->where('id',$active['m_id'])->select();
                }
                break;
            case 2:
                foreach ($active['apply'] as $vo=>$k){
                    $mer[]=Db::name('active_mer')->where('id',$k['apply_name'])->find();
                }
                break;
        }
        return [$active,$mer];
    }

    /**
     * 返回生成的JS内容
     * @return \think\Response
     * @throws \WeChat\Exceptions\InvalidResponseException
     * @throws \WeChat\Exceptions\LocalCacheException
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function jsSdk($url)
    {
        $url = $this->request->server('http_referer', $url, null);
        $wechat = WechatService::getWebOauthInfo($url, $this->request->get('mode', 1), false);
        $openid = isset($wechat['openid']) ? $wechat['openid'] : '';
        $unionid = empty($wechat['fansinfo']['unionid']) ? '' : $wechat['fansinfo']['unionid'];
        $configJson = json_encode(WechatService::getWebJssdkSign($url), JSON_UNESCAPED_UNICODE);
        $fansinfoJson = json_encode(isset($wechat['fansinfo']) ? $wechat['fansinfo'] : [], JSON_UNESCAPED_UNICODE);
        return [
            'openid' => $openid,
            'unionid' => $unionid,
            'configJso' => json_decode($configJson,true),
            'fansinfoJson' => json_decode($fansinfoJson,true),
        ];
    }


    /**
     * 发起支付
     */
    public function goPay()
    {

        if ($this->request->isAjax()){
            $active = Db::name('active_info')->where('id',$this->request->param('aid'))->find();
            if (empty($active)){
                return ['code'=>0,'info'=>'获取支付失败'];
            }
            if (empty($active['vouchers'])){
                return ['code'=>0,'info'=>'优惠券信息错误'];
            }
            $vouchers = json_decode($active['vouchers'],true);
            if (empty($vouchers[$this->request->param('id')])){
                return ['code'=>0,'info'=>'优惠券信息错误'];
            }
            if (session('user_info')){
                $pay = WechatService::WePayOrder();
                $openid = session('user_info.openid');
                $options = [
                    'body'             => '测试活动',
                    'out_trade_no'     => time(),
                    'total_fee'        => '1',
                    'openid'           => $openid,
                    'trade_type'       => 'JSAPI',
                    'notify_url'       => url('@wechat/api.tools/notify', '', true, true),
                    'spbill_create_ip' => request()->ip(),
                ];
                // 生成预支付码
                $result = $pay->create($options);
                // 创建JSAPI参数签名
                $options = $pay->jsapiParams($result['prepay_id']);
                //  $optionJSON = json_encode($options, JSON_UNESCAPED_UNICODE);
                // JSSDK 签名配置
                //    $configJSON = json_encode(WechatService::getWebJssdkSign(), JSON_UNESCAPED_UNICODE);
                return json(['option'=>$options,'config'=>WechatService::getWebJssdkSign()]);
            }else{
                return ['code'=>0,'info'=>'请重新登录'];
            }
        }
    }
}