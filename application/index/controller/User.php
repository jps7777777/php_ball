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

    public function __construct()
    {
        parent::__construct(true);
    }


    public function merge_user_info(){
        $mobile = input("mobile");
        $verify = input("code");
        $type = input("send_type",0);


        if(empty($mobile) || empty($verify)){
            $this->json("验证信息不能为空","error",0);
        }
        $bool = $this->verify_code($mobile,$verify,$type);
        if($bool !== true){
            $this->json("$bool","error",0);
        }
        $user_new = model("User")->get_info([['phone','eq',$mobile]],"*");
        if(empty($user_new)){
            $this->json("用户不存在","error",0);
        }

        $user_old = session("user_info");
        // TODO 合并用户信息
        foreach ($user_new as $a=>$b){
            if(empty($b) && isset($user_old[$a])){
                $user_new[$a] = $user_old[$a];
            }
        }

        session("user_info",$user_new);
    }


    public function user_think(){
        $i = 10000;
//        foreach (){
//
//        }


    }

    public function get_user_info(){
        $user = session("user_info");
        if(empty($user)){
            $this->json("用户信息不存在","error",0);
        }
        $user_info = model("User")->get_user_info_html($user['id']);
        $this->json($user_info);
    }




    public function logout(){
        session("user_info",null);
        $this->json("退出成功");
    }






}