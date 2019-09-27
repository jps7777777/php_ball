<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2019-8-22 0022
 * Time: 9:21
 */

namespace app\index\controller;

use http\Env\Url;
use think\Controller;
use think\facade\Env;
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
     * 二维码扫码登录
     * @author 金
     * @create time 2019-9-20 0020 11:12
     */
    public function qr_login(){

//        echo Env::get("vendor_path").'qrcode/phpqrcode.php';die;
        require_once Env::get("vendor_path")."qrcode\phpqrcode.php";



        //本地文档相对路径
        $url = dirname(__FILE__)."/image/";

        $value = 'http://www.login.com/qr_code_login?token=79878987';
        $errorCorrentionLevel = 'L'; //容错级别
        $matrixPoinSize = 20; //生成图片大小
        // 第一个参数，扫码内容
        // 第二个参数，二维码保存路径
        // 第三个参数，二维码容错率，不同的参数表示二维码可被覆盖的区域百分比
        //      默认为L，这个参数可传递的值分别是L(QR_ECLEVEL_L，7%)，
        //      M(QR_ECLEVEL_M，15%)，Q(QR_ECLEVEL_Q，25%)，H(QR_ECLEVEL_H，30%)
        // 第四个参数，控制生成图片的大小，默认为4
        // 第五个参数，控制生成二维码的空白区域大小
        // 第七个参数，前景颜色（十六进制）
        \QRcode::png($value,$url.'qrcode.png',$errorCorrentionLevel,$matrixPoinSize,2,true);
        //如不加logo，下面logo code 注释掉，输出$url.qrcode.png即可。
        $logo =$url.'LOGO.png'; //logo
        $QR = $url.'qrcode.png'; //已经生成的二维码

        if($logo !== FALSE){
            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);//二维码图片宽度
            $QR_height = imagesy($QR);//二维码图片高度
            $logo_width = imagesx($logo);//logo图片宽度
            $logo_height = imagesy($logo);//logo图片高度
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            //重新组合图片并调整大小
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
                $logo_qr_height, $logo_width, $logo_height);
        }

        //新图片
        $img = $url.'hello.png';
        //输出图片
        imagepng($QR, $img);
        echo "<img src=$img />";



//        echo QRimage::png("www.baidu.com");
//
//        QRcode::png();
//
//        echo 232;die;


    die;

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