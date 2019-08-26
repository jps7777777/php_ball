<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2019-8-14 0014
 * Time: 9:33
 */

namespace app\common\model;


class User extends Base
{


    /**
     * 网页访问获取用户信息
     * @author 金
     * @create time 2019-8-26 0026 11:00
     * @param $user_id
     * @return array|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_user_info_html($user_id){
        $data = $this->alias('a')
            ->join("user_info b","a.id=b.user_id","left")
            ->where("a.id",'eq',$user_id)
            ->field("a.id,a.avatar,a.nick_name,a.email,a.phone,a.age,a.gender,b.balance,b.role")
            ->find();
        if(empty($data)){
            return null;
        }
        return $data->toArray();
    }



}