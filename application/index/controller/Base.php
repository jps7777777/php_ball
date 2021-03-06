<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2019-8-13 0013
 * Time: 16:02
 */

namespace app\index\controller;


use think\Controller;
use think\Request;

class Base extends Controller
{
    public function __construct($flag = false)
    {
//        parent::__construct(new Request);
        header('Access-Control-Allow-Origin:*');

        if($flag){
            $this->check_login();
        }
    }

    /**
     * 验证用户是否登录
     * @author 金
     * @create time 2019-8-26 0026 11:45
     */
    protected function check_login(){
        $user = session("user_info");
        if(empty($user)){
            $token = input("login_token");
            $user_db = model("User")->get_info([['token','eq',$token]],'*');
            if(empty($user_db)){
                $this->json("用户不存在","error",0);
            }
            session("user_info",$user_db);
        }
    }

    /**
     * 数据统一分装返回json
     * @param null $info
     * @param string $message
     * @param string $status
     */
    protected function json($info = null, $message = "SUCCESS", $status = "1") {
        $data['status'] = $status;
        $data['message'] = $message;
        $data['time'] = date("Y-m-d H:i:s");
        $data['data'] = $info;
        // 返回JSON数据格式到客户端 包含状态信息
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data, JSON_UNESCAPED_UNICODE));
    }


}