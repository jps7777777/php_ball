<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2019-8-14 0014
 * Time: 16:02
 */

namespace app\index\controller;


class Game extends Base
{

    public function index()
    {
        $this->method_one();


//        $this->update_result();
//        $user_id = input("user_id");
//        if (empty($user_id)) {
//            $this->json("用户不存在");
//        }
//        $this->add_result($user_id,8456,3);
    }

    /**
     * 根据平均值进行投入
     * 选择平均出现次数的数值进行投注
     * 随机选择
     * @author 金
     * @create time 2019-8-14 0014 16:10
     */
    public function method_one()
    {
        $data = $this->get_match_statistics();
        $total = array_sum($data['appear_total']);
        $cal_info = [];
        $near_num = 10;
        $choose = 0;
        foreach ($data['appear_total'] as $a=>$b){
            $tmp = number_format(($b/$total)*100,2,'.','');
            $tmp_cal = abs($b-10);
            if($tmp_cal<$near_num){
                $choose = $a;
                $near_num = $tmp_cal;
            }
            $cal_info[$a] = $tmp;
        }



    }

    /**
     * 获取最后一次数据结果集
     * @author 金
     * @create time 2019-9-27 0027 9:54
     */
    public function get_last_result()
    {
        $user_id = input("user_id");
        if (empty($user_id)) {
            $this->json("用户不存在");
        }
        $user_info = model("Result")->get_info([['user_id', 'eq', $user_id]], "*");
        $this->json($user_info);
    }

    /**
     * 获取比赛数据分析值
     * @author 金
     * @create time 2019-9-27 0027 9:53
     * @return mixed
     */
    public function get_match_statistics()
    {
        $old_match = model("Ball")->get_list_by_page('', '*', 'create_time desc', 0, 60);
        $sort_match = [];
        $total = ["0" => 0, "1" => 0, "2" => 0, "3" => 0, "4" => 0, "5" => 0, "6" => 0, "7" => 0, "8" => 0, "9" => 0];
        foreach ($old_match as $i => $j) {
            $sort_match[$j['id']][1] = $j['number_one'];
            $sort_match[$j['id']][2] = $j['number_two'];
            $sort_match[$j['id']][3] = $j['number_three'];
            $sort_match[$j['id']][4] = $j['number_four'];
            $sort_match[$j['id']][5] = $j['number_five'];
            $total = $this->cal_appear_total($sort_match[$j['id']], $total);
        }
        $res['appear_total'] = $total;
        $res['last_appear'] = $this->cal_last_appear($sort_match);;
        $res['match'] = $sort_match;
        return $res;
    }

    /**
     * 返回最新60次结果集及出现次数、最后数值出现次数
     * @author 金
     * @create time 2019-9-27 0027 9:52
     */
    public function cal_old_match()
    {
        $old_match = model("Ball")->get_list_by_page('', '*', 'create_time desc', 0, 60);
        $sort_match = [];
        $total = ["0" => 0, "1" => 0, "2" => 0, "3" => 0, "4" => 0, "5" => 0, "6" => 0, "7" => 0, "8" => 0, "9" => 0];
        foreach ($old_match as $i => $j) {
            $sort_match[$j['id']][1] = $j['number_one'];
            $sort_match[$j['id']][2] = $j['number_two'];
            $sort_match[$j['id']][3] = $j['number_three'];
            $sort_match[$j['id']][4] = $j['number_four'];
            $sort_match[$j['id']][5] = $j['number_five'];
            $total = $this->cal_appear_total($sort_match[$j['id']], $total);
        }
        $res['appear_total'] = $total;
        $res['last_appear'] = $this->cal_last_appear($sort_match);;
        $res['match'] = $sort_match;
        $this->json($res);
    }

    /**
     *
     * @author 金
     * @create time 2019-8-19 0019 13:52
     * @param $user_id
     * @param $issue
     * @param $num
     * @param $ball
     * @return bool|string
     */
    private function add_result($user_id, $issue, $num,$ball = 1)
    {
        if (empty($user_id) || empty($num) || empty($issue)) {
            return "选择信息错误";
        }
        $info = [
            'user_id' => $user_id,
            'issue' => $issue,
            'choose_number' => $num,
            'pay' => 10,
            'count' => 1,
            'choose_ball' => $ball,
        ];
        $where_last = [
            ["user_id", 'eq', $user_id],
            ["earnings", 'eq', 0],
        ];
        $old = model("Result")->get_info($where_last, "*", "create_time desc");
        if (!empty($old) && $old['issue'] == ($issue - 1)) {
            $info['pay'] = $old['pay'] * 3;
            $info['count'] = intval($old['pay']) + 1;
        }
        model("Result")->save($info);
        return true;
    }

    /**
     * 修改结果
     * @author 金
     * @create time 2019-9-27 0027 9:50
     * @return string
     * @throws \Exception
     */
    private function update_result()
    {
        $res = model("Ball")->where('id','eq',8477)->order("create_time desc")->find();
//        $res = model("Ball")->order("create_time desc")->find();
        if (count($res) > 0) {
            $res = $res->toArray();
        } else {
            return "没有跟新内容";
        }
        $where = [
            ['issue', 'eq', $res['id']],
            ['result', 'eq', 0]
        ];
        $res_pay = model("Result")->get_list($where, "id,choose_number,choose_ball,user_id,pay,count,earnings,result,issue");
        if (empty($res_pay)) {
            return "没有投注";
        }
        $all = [];
        foreach ($res_pay as $i => $j) {
            $verify = $res[$this->get_ball_num($j['choose_ball'])];
            $tmp['id']=$j['id'];
            if($j['choose_number'] == $verify){
                $tmp['result']= 1;
                $tmp['earnings'] = $j['pay']*9.5;
            }else{
                $tmp['result']= 2;
            }
            $all[] = $tmp;
            unset($tmp);
        }
        model("Result")->saveAll($all);
        return "ok";
    }

    /**
     * 获取num序号对应的值
     * @author 金
     * @create time 2019-9-27 0027 9:50
     * @param $num
     * @return mixed
     */
    private function get_ball_num($num){
        $arr = [1=>'number_one',2=>'number_two',3=>'number_three',4=>'number_four',5=>'number_five'];
        return $arr[$num];
    }


    /**
     * 计算出现的次数
     * @author 金
     * @create time 2019-9-27 0027 9:51
     * @param $res
     * @param $total
     * @return mixed
     */
    private function cal_appear_total($res, $total)
    {
        foreach ($res as $a => $b) {
            $total[$b] = $total[$b] + 1;
        }
        return $total;
    }

    /**
     * 计算最后出现的次数
     * @author 金
     * @create time 2019-9-27 0027 9:51
     * @param $res
     * @return array
     */
    private function cal_last_appear($res)
    {
        $total = ["0" => 0, "1" => 0, "2" => 0, "3" => 0, "4" => 0, "5" => 0, "6" => 0, "7" => 0, "8" => 0, "9" => 0];
        $item = 1;
        foreach ($res as $a => $b) {
            foreach ($b as $i => $j) {
                if ($total[$j] == 0) {
                    $total[$j] = $item;
                }
            }
            $item++;
        }
        return $total;
    }



}