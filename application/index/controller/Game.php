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



    public function cal_old_match(){
        $old_match = model("Ball")->get_list_by_page('','*','create_time desc',0,60);
        $sort_match = [];
        $total = ["0"=>0,"1"=>0,"2"=>0,"3"=>0,"4"=>0,"5"=>0,"6"=>0,"7"=>0,"8"=>0,"9"=>0];
        $times = 0;
        foreach ($old_match as $i=>$j){
            $sort_match[$j['id']][1] = $j['number_one'];
            $sort_match[$j['id']][2] = $j['number_two'];
            $sort_match[$j['id']][3] = $j['number_three'];
            $sort_match[$j['id']][4] = $j['number_four'];
            $sort_match[$j['id']][5] = $j['number_five'];
            $total = $this->cal_appear_total($sort_match[$j['id']],$total);
//            if($times < 3){
//                $
//            }
        }
        $res['appear_total'] = $total;
        $res['last_appear'] = $this->cal_last_appear($sort_match);;
        $res['match'] = $sort_match;
        $this->json($res);
    }

    private function cal_appear_total($res,$total){
        foreach ($res as $a=>$b){
            $total[$b] = $total[$b]+1;
        }
        return $total;
    }

    private function cal_last_appear($res){
        $total = ["0"=>0,"1"=>0,"2"=>0,"3"=>0,"4"=>0,"5"=>0,"6"=>0,"7"=>0,"8"=>0,"9"=>0];
        $item = 1;
        foreach ($res as $a=>$b){
            foreach ($b as $i=>$j){
                if($total[$j] == 0){
                    $total[$j] = $item;
                }
            }
            $item++;
        }
        return $total;
    }



    /**
     * 根据平均值进行投入
     * @author 金
     * @create time 2019-8-14 0014 16:10
     */
    public function method_one(){











    }














}