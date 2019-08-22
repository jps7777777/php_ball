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




    public function user_think(){
        $i = 10000;
//        foreach (){
//
//        }


    }


    public function get_user_info(){
        $user = session("user_info");
        $this->json($user);
    }

    public function logout(){
        session("user_info",null);
        $this->json("退出成功");
    }
    







}