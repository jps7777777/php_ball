<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2019-8-13 0013
 * Time: 17:10
 */

namespace app\index\controller;


use think\facade\Request;

class User extends Base
{



    



















    public function add_user(){
        $phone = input("mobile");
        $password = input("password");

        $data = model("User")->get_info([['phone','eq',$phone]],"id,phone");
        if(!empty($data)){
            $this->json("","用户已存在",0);
        }
        model("User")->startTrans();
        $user = ['phone'=>$phone,
            'account'=>substr(Request::token(time().'-'.random_int(1000,9999), 'md5'),0,12)
        ];
        model("User")->save($user);
        $last_id = model("User")->getLastInsID();
        $user_login = [
            'user_id'=>$last_id,
            'credential'=>md5(sha1($password."self")),
            'login_type'=>0
        ];
        $flag_1 = model("UserLogin")->save($user_login);
        $user_info = [
            'user_id'=>$last_id,
            'balance'=>1000000
        ];
        $flag_2 = model("UserInfo")->save($user_info);
        if($flag_1 && $flag_2){
            model("User")->commit();
            $this->json("添加成功");
        }else{
            model("User")->rollback();
            $this->json("添加失败了。");
        }
    }






}