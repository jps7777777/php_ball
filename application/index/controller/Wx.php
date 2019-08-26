<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2019-8-23 0023
 * Time: 10:08
 */

namespace app\index\controller;


use think\facade\Request;

class Wx
{

    /**
     * 微信登录
     * @author 金
     * @create time 2019-8-23 0023 9:18
     */
    public function wx_login(){
        $res = $this->get_user_info("","");
        $where = [
            ['credential','eq',$res['unionid']],
            ['login_type','eq',2],
        ];
        $user_login = model("UserLogin")->get_info($where,"*");
        if(empty($user_login)){
            $user = $this->save_wx_user_html($res);
        }else{
            $user = model("User")->get_info([['id','eq',$user_login['id']]],"*");
        }
        if(empty($user)){
            $this->json("登录失败了","服务器错误",0);
        }
        session("user_info",$user);
        $this->json("登录成功");
    }

    private function save_wx_user_html($res){
        model("User")->startTrans();
        // 保存图片
        // $user['avatar'] =
        $user['nick_name'] = $res['nickname'];
        $user['gender'] = $res['sex'];
        $user['nick_name'] = $res['nickname'];
        $user['account'] = substr(Request::token(time().'-'.random_int(1000,9999), 'md5'),0,12);
        model("User")->save($user);
        $user_id = model("User")->getLastInsID();

        $user_login = [
            'user_id'=>$user_id,
            'credential'=>$res['unionid'],
            'login_type'=>2
        ];
        $flag_1 = model("UserLogin")->save($user_login);
        $user_info = [
            'user_id'=>$user_id,
            'balance'=>1000000,
            'country'=>$res['country'],
            'province'=>$res['province'],
            'city'=>$res['city'],
        ];
        $flag_2 = model("UserInfo")->save($user_info);
        if($flag_1 && $flag_2){
            model("User")->commit();
            $user['id'] = $user_id;
            return $user;
        }else{
            model("User")->rollback();
            return null;
        }
    }

    /**
     * 获取用户允许登录的凭证
     * 保存2小时
     * @author 金
     * @create time 2019-8-26 0026 9:41
     * @return mixed
     */
    public function get_code(){

//        $url = "https://open.weixin.qq.com/connect/qrconnect?appid=APPID&redirect_uri=REDIRECT_URI
//        &response_type=code&scope=SCOPE&state=STATE#wechat_redirect";
//        $url = "https://open.weixin.qq.com/connect/qrconnect?appid=wxbdc5610cc59c1631
//            &redirect_uri=https%3A%2F%2Fpassport.yhd.com%2Fwechat%2Fcallback.do&response_type=code
//            &scope=snsapi_login&state=3d6be0a4035d839573b04816624a415e#wechat_redirect";
//        $res = http_get_url($url);
//        var_dump($res);
//        $error_res = "{\"errcode\":40029,\"errmsg\":\"invalidcode\"}";
        $right_res = "{ 
            \"access_token\":\"ACCESS_TOKEN\", 
            \"expires_in\":7200, 
            \"refresh_token\":\"REFRESH_TOKEN\",
            \"openid\":\"OPENID\", 
            \"scope\":\"SCOPE\",\"unionid\":\"o6_bmasdasdsad6_2sgVt7hMZOPfL\"}";
        return json_decode($right_res,true);

    }

    /**
     * 当access_token过期后，可以使用refresh_token方法更新access_token
     * refresh_token可以保存30天。
     * @author 金
     * @create time 2019-8-26 0026 9:40
     * @return mixed
     */
    public function get_refresh_token(){
        $url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=APPID&grant_type=refresh_token&refresh_token=REFRESH_TOKEN";
        $right_res = "{ 
            \"access_token\":\"ACCESS_TOKEN\", 
            \"expires_in\":7200, 
            \"refresh_token\":\"REFRESH_TOKEN\", 
            \"openid\":\"OPENID\", 
            \"scope\":\"SCOPE\" 
            }";
        return json_decode($right_res,true);
    }

    /**
     * 使用access_token获取用户信息
     * @author 金
     * @create time 2019-8-26 0026 9:50
     * @param $access_token
     * @param $open_id
     * @return mixed
     */
    public function get_user_info($access_token,$open_id){
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$open_id";
        $res = http_get_url($url);
        $right_res = "{ 
            \"openid\":\"OPENID\",
            \"nickname\":\"造船厂\",
            \"sex\":1,
            \"province\":\"天津\",
            \"city\":\"天津\",
            \"country\":\"中国\",
            \"headimgurl\":\"http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0\",
            \"privilege\":[
            \"PRIVILEGE1\", 
            \"PRIVILEGE2\"
            ],
            \"unionid\": \" o6_bmasda5sdsad6_2sgVtMZOPfL\"
            } ";
        return json_decode($right_res,true);
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
        $data['time'] = time();
        $data['data'] = $info;
        // 返回JSON数据格式到客户端 包含状态信息
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

}