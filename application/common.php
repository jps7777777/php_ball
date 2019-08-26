<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


function http_get_url($url)
{
    //初始化
    $curl = curl_init();
    //设置参数
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

//function http_post_url($url){
//    //初始化
//    $curl = curl_init();
//    //设置参数
//    curl_setopt($curl, CURLOPT_URL, $url);
//    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//    curl_setopt($curl, CURLOPT_FAILONERROR, false);
//    $result = curl_exec($curl);
//    curl_close($curl);
//    return $result;
//}


function send_verify_code($mobile, $send_type)
{
    $type = isset($send_type) ? $send_type : 0;
    if (empty($mobile)) {
        return "电话号码不能为空";
    }
    $code = random_int(100000, 999999);
    $data = model("VerifyCode")->get_info([['mobile', 'eq', $mobile], ['type', 'eq', $type]], "*");
    if (empty($data)) {
        $data = [];
        $data['mobile'] = $mobile;
        $data['count'] = 1;
        $data['type'] = $type;
        $data['code'] = $code;
        model("VerifyCode")->save($data);
        return "验证信息已发送";
    }
    if (isset($data) && $data['count'] > 19) {
        return "今日验证次数过多";
    }
    if (isset($data) && abs(time() - strtotime($data['update_time'])) < 60) {
        return "发送过于频繁，请稍后再试";
    }
    $save_data = [];
    $save_data['id'] = $data['id'];
    $save_data['count'] = $data['count'] + 1;
    $save_data['code'] = $code;
    $save_data['is_use'] = 0;
    model("VerifyCode")->isUpdate(true)->save($save_data);
    return true;
}

function verify_code($mobile, $code, $send_type = 0)
{
    if (empty($mobile) || empty($code) || strlen($code) < 6) {
        return "验证信息不正确";
    }
    $data = model("VerifyCode")->get_info([['mobile', 'eq', $mobile], ['type', 'eq', $send_type], ['is_use', 'eq', 0]], "*");
    if (empty($data)) {
        return "验证码不存在";
    }
    if (abs(time() - strtotime($data['update_time'])) > 600) {
        return "验证码已过期";
    }
    if ($code != $data['code']) {
        return "验证码输入错误";
    }
    $save_data['id'] = $data['id'];
    $save_data['is_use'] = 1;
    model("VerifyCode")->isUpdate(true)->save($save_data);
    return true;
}










