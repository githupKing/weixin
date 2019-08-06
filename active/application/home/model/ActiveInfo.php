<?php
/**
 * Created by PhpStorm.
 * @User: 嘿嘿<skwordss@163.com>
 * Date: 2019/7/25 0025
 * Time: 1:25
 */

namespace app\home\model;

use think\Model;

class ActiveInfo extends Model
{
    /**
     * @name {ActivePacket} 关联活动红包关注
     * @Date {2019/7/25 0025}
     * @author 嘿嘿 <skwordss@163.com>
     * @copyright v0.1
     * @since {2019/7/25 0025}-宋康-创建
     * @return mixed
     */
    public function ActivePacket()
    {
        return $this->hasOne('ActivePacket','a_id','id');
    }

    /**
     * @name {ActiveShare} 关联活动红包排行
     * @Date {2019/7/25 0025}
     * @author 嘿嘿 <skwordss@163.com>
     * @copyright v0.1
     * @since {2019/7/25 0025}-宋康-创建
     * @return mixed
     */
    public function ActiveShare()
    {
        return $this->hasMany('ActiveShare','a_id','id');
    }

    /**
     * @name {ActiveVouchers} 关联活动优惠信息
     * @Date {2019/7/25 0025}
     * @author 嘿嘿 <skwordss@163.com>
     * @copyright v0.1
     * @since {2019/7/25 0025}-宋康-创建
     * @return mixed
     */
    public function ActiveVouchers()
    {
        return $this->hasOne('ActiveVouchers','a_id','id');
    }

    /**
     * @name {ActiveMer} 关联获得发起商家
     * @Date {2019/7/25 0025}
     * @author 嘿嘿 <skwordss@163.com>
     * @copyright v0.1
     * @since {2019/7/25 0025}-宋康-创建
     * @return mixed
     */
    public function ActiveMer()
    {
        return $this->hasOne('ActiveMer','id','m_id');
    }

    /**
     * @name {ActivePacketList} 获取活动红包排行
     * @Date {2019/7/25 0025}
     * @author 嘿嘿 <skwordss@163.com>
     * @copyright v0.1
     * @since {2019/7/25 0025}-宋康-创建
     * @return mixed
     */
    public function ActivePacketList()
    {
        return $this->hasOne('ActivePacketList','id','a_id');
    }
}