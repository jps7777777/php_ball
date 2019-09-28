<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2019-8-22 0022
 * Time: 11:18
 */

namespace app\index\controller;


use think\facade\Env;

/**
 * 记录用户操作记录，
 * 提取用户操作行为，分析用户玩游戏时心理情景
 * 情景分析：
 *      1、购买的土地多
 *      2、建设的房子多
 *      3、先购买一定数量的土地，在修建大量的住房
 *      4、有钱就买土地，买到土地就建房
 * Class Log
 * @package app\index\controller
 */
class Log extends Base
{

    /**
     * 保存自己想要的日志数据
     * @author 金
     * @create time 2019-9-28 0028 11:03
     */
    public function insert_log(){
        $param = input();
        $path = Env::get("root_path")."public\\log\\".date("Ymd").'.txt';
        $token = $param['token'];
        $str = "\n[".date("Y-m-d H:i:s")."]".$token."{";
        foreach ($param as $a=>$b){
            $str .= "\"".$a."\":\"".$b."\",";
        }
        $str = trim($str,",") ."}";
        $file = fopen($path,"a+");
        fwrite($file,$str);
        fclose($file);
    }

    /**
     * 提取自己需要的数据
     * @author 金
     * @create time 2019-9-28 0028 11:03
     */
    public function get_info(){
//        echo strlen("[2019-09-28 11:06:25]");die;

        $date = input("date");
        if(empty($date)){
            $date = date("Ymd");
        }
        $path = Env::get("root_path")."public\\log\\".$date.'.txt';

        $lines_arr=file($path,FILE_IGNORE_NEW_LINES);
        $ana = [];
        foreach ($lines_arr as $a=>$b){
//            echo strchr($b,"]");die;
            $f_index = stripos($b,"]");
            $s_index = stripos($b,"{");

            $key = substr($b,stripos($b,"]")+1,($s_index-$f_index)-1);
            $value = substr($b,$s_index);
            $date = substr($b,1,$f_index-1);
            $ana[$key][$date] = json_decode($value,true);
        }
    }




}