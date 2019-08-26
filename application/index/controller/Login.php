<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2019-8-22 0022
 * Time: 9:21
 */

namespace app\index\controller;

use think\Controller;
use think\facade\Request;

class Login extends Controller
{

    /**
     * 发送短信验证码
     * @author 金
     * @create time 2019-8-22 0022 10:06
     * @throws \Exception
     */
    public function send_verify_code()
    {
        $mobile = input("mobile");
        $type = input("send_type",0);
        if (empty($mobile)) {
            $this->json("电话号码不能为空", "error", 0);
        }
        $flag = send_verify_code($mobile,$type);
        if($flag !== true) {
            $this->json($flag, "error", 0);
        }
        $this->json("验证短信已发送");
    }

    /**
     * 用户注册
     * @author 金
     * @create time 2019-8-23 0023 9:19
     * @throws \think\exception\PDOException
     */
    public function register(){
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

    /**
     * 本地登录
     * @author 金
     * @create time 2019-8-23 0023 9:18
     */
    public function login_html(){
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
        $user = model("User")->get_info([['phone','eq',$mobile]],"*");
        if(empty($user)){
            $this->json("用户不存在","error",0);
        }
        session("user_info",$user);
        $this->json("登录成功");
    }

    /**
     * 本地登录
     * @author 金
     * @create time 2019-8-23 0023 9:18
     */
    public function login_interface(){
        $mobile = input("mobile");
        $verify = input("code");
        $type = input("send_type",0);
        if(empty($mobile) || empty($verify)){
            $this->json("验证信息不能为空","error",0);
        }
        $bool = verify_code($mobile,$verify,$type);
        if($bool !== true){
            $this->json("$bool","error",0);
        }
        $user = model("User")->get_info([['phone','eq',$mobile]],"*");
        if(empty($user)){
            $this->json("用户不存在","error",0);
        }
        $token = Request::token(time().'-'.random_int(1000,9999), 'md5');
        $save_user = [
            'id'=>$user['id'],
            'token'=>$token
        ];
        model("User")->isUpdate(true)->save($save_user);
        session("$token",$user);
        $return = [
            'token'=>$token,
            'nick_name'=>$user['nick_name'],
            'account'=>$user['account'],
            'avatar'=>$user['avatar'],
            'phone'=>$user['phone']
        ];
        $this->json($return);
    }

    /**
     * 验证短信验证码
     * @author 金
     * @create time 2019-8-22 0022 10:06
     * @param $mobile
     * @param $code
     * @param $send_type
     * @return bool
     */
//    private function verify_code($mobile,$code,$send_type = 0){
//        if (empty($mobile) || empty($code) || strlen($code)<6) {
//            return "验证信息不正确";
//        }
//        $data = model("VerifyCode")->get_info([['mobile', 'eq', $mobile], ['type', 'eq', $send_type],['is_use','eq',0]], "*");
//        if(empty($data)){
//            return "验证码不存在";
//        }
//        if(abs(time() - strtotime($data['update_time'])) > 600){
//            return "验证码已过期";
//        }
//        if($code != $data['code']){
//            return "验证码输入错误";
//        }
//        $save_data['id'] = $data['id'];
//        $save_data['is_use'] = 1;
//        model("VerifyCode")->isUpdate(true)->save($save_data);
//        return true;
//    }


    /**
     * 数据统一分装返回json
     * @param null $info
     * @param string $message
     * @param string $status
     */
    private function json($info = null, $message = "SUCCESS", $status = "1")
    {
        $data['status'] = $status;
        $data['message'] = $message;
        $data['time'] = time();
        $data['data'] = $info;
        // 返回JSON数据格式到客户端 包含状态信息
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

}