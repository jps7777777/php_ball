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

    public function send_merge_code(){
        $mobile = input("mobile");
        if (empty($mobile)) {
            $this->json("电话号码不能为空", "error", 0);
        }
        $flag = send_verify_code($mobile,3);
        if($flag !== true) {
            $this->json($flag, "error", 0);
        }
        $this->json("验证短信已发送");
    }

    public function merge_user_info_with_wx(){
        $mobile = input("mobile");
//        $verify = input("code");

//        if(empty($mobile) || empty($verify)){
//            $this->json("验证信息不能为空","error",0);
//        }
//        $bool = verify_code($mobile,$verify,3);
//        if($bool !== true){
//            $this->json("$bool","error",0);
//        }
        $user_new = model("User")->get_info([['phone','eq',$mobile]],"*");
        if(empty($user_new)){
            $this->json("用户不存在","error",0);
        }
        var_dump($user_new);
        $user_old = session("user_info");// 微信用户的信息
        // 合并User表信息
        foreach ($user_new as $a=>$b){
            if(empty($b) && isset($user_old[$a])){
                $user_new[$a] = $user_old[$a];
            }
        }
//        model("User")->startTrans();
//        model("User")->isUpdate(true)->save($user_new);
        // 合并UserLogin表信息
        $where = [
            ['user_id','eq',$user_old['id']],
            ['login_type','eq',2],
        ];
        $login_old = model("UserLogin")->get_info($where,"id,login_type");
        $login_new = [
            'id'=>$login_old['id'],
            'user_id'=>$user_new['id']
        ];
        $flag_1 = model("UserLogin")->isUpdate(true)->save($login_new);
        // 合并UserInfo表信息
        $info_old = model("UserInfo")->get_info([['user_id','eq',$user_old['id']]],"*");
        $info_new = model("UserInfo")->get_info([['user_id','eq',$user_new['id']]],"*");
        foreach ($info_new as $a=>$b){
            if($a == "balance"){
                $info_new['balance'] = $b+$info_old['balance'];
                continue;
            }
            if(empty($b) && isset($info_old[$a])){
                $info_new[$a] = $info_old[$a];
            }
        }
        $flag_2 = model("UserInfo")->isUpdate(true)->save($info_new);
        if($flag_1 && $flag_2){
            session("user_info",$user_new);
            $this->json("用户信息合并成功");
        }
        $this->json("用户信息合并失败",'error',0);
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