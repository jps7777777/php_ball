<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2019-8-13 0013
 * Time: 16:02
 */

namespace app\index\controller;


use think\Controller;

class Base extends Controller
{
    public function __construct()
    {
        parent::__construct(null);
        header('Access-Control-Allow-Origin:*');
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