<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2019-9-27 0027
 * Time: 17:16
 */

namespace app\index\controller;


use think\App;
use think\facade\Env;

class Qrcode extends Base
{

    /**
     * 移动端扫描信息后，访问后台，把用户信息修改了。
     * @author 金
     * @create time 2019-9-29 0029 11:34
     */
    public function mobile_call(){
        /**
         * 1、接收参数，
         * 2、修改用户信息
         */
        $mobile_token = input("ms_token");
        $uuid_token = input("uuid_token");

        $redis = new \Redis();
        $redis->connect("47.105.151.214","6379",60);
        $redis->set($mobile_token,"");
        $data = $redis->keys("*");

    }


    public function ali_code_param(){

        $arc = input("_arc_");
        $cur = input("__token__");

        $redis = new \Redis();
        $redis->connect("47.105.151.214","6379",60);
        $data = $redis->keys("*");
        var_dump($data);
        /**
         * 1、先获取
         */
        $rule = model("district")->get_list(["code"=>15],"*");
        var_dump($rule);


        $token = input("token");
        $token = input("_qrl_token_");
        $user = $redis->get($token);
        if(empty($user)){
            return "";
        }



        /**
         * appName: aliyun
        fromSite: -2
        appEntrance: aliyun
        appName: aliyun
        bizParams:
        csrf_token: mXCFn5234BgCxyIBmzzSRG
        fromSite: -2
        hsiz: 1ddc537ae54eec57f1b4eeec574ab24f
        isMobile: false
        lang: zh_CN
        mobile: false
        returnUrl: http://account.aliyun.com/login/login_aliyun.htm?oauth_callback=https%3A%2F%2Fwww.aliyun.com%2F
        umidToken: 166143f0bf64fecfaeddd870e28cc29fa873970f
         */
        /**
         * appName: aliyun
        fromSite: -2
        appEntrance: aliyun
        appName: aliyun
        bizParams:
        csrf_token: mXCFn5234BgCxyIBmzzSRG
        fromSite: -2
        hsiz: 1ddc537ae54eec57f1b4eeec574ab24f
        isMobile: false
        lang: zh_CN
        mobile: false
        returnUrl: http://account.aliyun.com/login/login_aliyun.htm?oauth_callback=https%3A%2F%2Fwww.aliyun.com%2F
        umidToken: 166143f0bf64fecfaeddd870e28cc29fa873970f
         */
    }



    /**
     * 二维码扫码登录
     * www.ball.com/public/image/hello.png // 图片路径
     * @author 金
     * @create time 2019-9-20 0020 11:12
     */
    public function generate_qr_code(){
//        echo Env::get('app_path');die;
        require_once Env::get("vendor_path")."qrcode\phpqrcode.php";

        //本地文档相对路径
        $url = Env::get('app_path')."/image/";

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

        // 返回路径加密信息，然后解密得到路劲





        // 1ddc537ae54eec57f1b4eeec574ab24f
        // 1ddc537ae54eec57f1b4eeec574ab24f

        // url : https://img.alicdn.com/imgextra/O1CN01jCfrmn2DMYkO7HTWs_!!1855378595-2-xcode.png
        // url : https://img.alicdn.com/imgextra/O1CN01L31kdq1rSaPenBIli_!!399715630-2-xcode.png

        // https://hd.m.aliyun.com/act/version-updates.html?
        //closeWindow=100&
        //gotoUrl=aliyun%3a%2f%2fforward%2f10f5510b2f247517efde2c530e318a63%3ftarget_%3d%2fscan%2flogin%26
        //token%3d130677401743247e17d9a4c91201d55ff_0000000&_from=havana

        // https://hd.m.aliyun.com/act/version-updates.html?closeWindow=100
        //&gotoUrl=aliyun%3a%2f%2fforward%2f10f5510b2f247517efde2c530e318a63%3ftarget_%3d%2fscan%2f
        //login%26token%3d1aa2cce8502fd9d3a33c5d47cad743bda_0000000&_from=havana

        //新图片
        $img = $url.'hello.png';
        //输出图片
        imagepng($QR, $img);
        echo "<img src=$img />";



//        echo QRimage::png("www.baidu.com");
//
//        QRcode::png();
//
//        echo 232;die;path
        $path = "";
        $this->json($path);
    }

}