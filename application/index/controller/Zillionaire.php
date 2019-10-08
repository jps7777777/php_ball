<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2019-9-27 0027
 * Time: 10:51
 */

namespace app\index\controller;




class Zillionaire extends Base
{
    protected $redis;
    // 红橙黄绿青蓝紫
    // red orange yellow malachite green bluish violet
    // 红橙黄绿蓝靛紫
    // Red orange yellow green blue indigo violet
    protected $user_color = [
        1=>"red",2=>"orange",3=>"yellow",4=>"green",5=>"blue",6=>"indigo",7=>"violet"
    ];
    protected $user_init = [
        "money"=>2000,
        "logo"=>0,// 用户图标:0没有图标及颜色
        "color"=>'white',// 用户图标:0没有图标及颜色
        "sequence"=>0,// 用户顺序
        "city"=>"",// 持有土地:城市名,城市名
        "pledge"=>"",// 抵押房产：城市名，城市名
        "prison_break_card"=>false,
        "status"=>1,// 0等待，1正常，2暂停一次，3破产
        "money_card"=>"5000-0,2000-1,1000-0,500-0,200-0,100-0,50-0,20-0,10-0",//金钱面值-持有数量，金钱面值-持有数量，
    ];

    public function __construct($flag = false)
    {
        parent::__construct($flag);
        $this->redis = new \Redis();
        $this->redis->connect("47.105.151.214","6379","60");
    }

    /**
     * 1、登录网站use_token()
     *      2、添加用户名get_user_name()
     * 2、创建房间/登录房间
     * 3、开始游戏
     *
     */


    /**
     * 房间：713033
     *      user_id:4306467515c76:老大
     *      user_id:fdef270e3cb89:王二
     *      user_id:aa89f552a1217:张三
     *      user_id:1a5dcc3ca1ec2:李四
     *
     *
     *
     */


    /**
     * 开始游戏
     * 进入网站，获得用户编号
     * 1、 用户注册用户名
     * 2、
     * @author 金
     * @create time 2019-9-27 0027 11:37
     */
    public function index(){
        $user_id = input("token");
        if(empty($user_id)){
            $this->json("请先登录");
        }
        $table_id = input("table_id");
        if(empty($table_id)){
            $this->json("房间不存在");
        }
        $data = $this->redis->hGetAll($table_id);
        if(empty($data) ){
            $this->json("房间不存在");
        }
        if($data['user_num'] > $data['user_now']){
            $this->json("玩家还没到齐啊，等等再开始啦。");
        }
        if($data['user_num'] < $data['user_now']){
            $this->json("请重新开始牌局。");
        }
        // 初始化用户信息：分配金钱，初始化持卡，初始化越狱卡持有状态
        $k = 1;
        $users = json_decode($data['users'],true);
//        foreach ($users as $a =>$b){
//            $this->user_init['table_id'] = $table_id;
//            $this->user_init['sequence'] = $k;
//            $this->user_init['logo'] = $k;
//            $this->user_init['color'] = $this->user_color[$k];
//            $this->redis->hMSet($b,$this->user_init);
//            $k++;
//        }
        $map = $this->get_map(count($users));
        var_dump($map);die;
        $this->redis->hSet($table_id,"map",$map);
    }

    /**
     * 用户登录
     * 5eae5a3ee
     * b3a819a79fbdd
     * @author 金
     * @create time 2019-9-27 0027 11:40
     */
    public function use_token(){
        $user_id = substr(\Request::token(),1,13);
        // 保存用户信息
        $this->redis->hSet($user_id,'name',$user_id);
        $this->redis->hSet($user_id,'table_id',0);
        $this->json($user_id);
    }

    /**
     * 创建游戏房间
     * @author 金
     * @create time 2019-9-27 0027 13:51
     * @throws \Exception
     */
    public function create_table(){
        $token = $this->login_status();
        // 创建房间号并保存
        $table_id = random_int(100000,999999);
        $this->redis->hSet($token,"table",$table_id);
        // 保存房间信息
        $user_num = input("num");
        if(empty($user_num)){
            $user_num = 4;
        }
        $this->redis->hSet($table_id,"user_num",$user_num);
        $this->redis->hSet($table_id,"user_now",1);
        $this->redis->hSet($table_id,"users",json_encode([$token]));
        $this->json("创建房间号:".$table_id);
    }




    /**
     * 加入房间
     * @author 金
     * @create time 2019-10-8 0008 11:48
     */
    public function insert_table(){
        $user_id = input("token");
        if(empty($user_id)){
            $this->json("请先登录");
        }
        $table_id = input("table_id");
        if(empty($table_id)){
            $this->json("房间不存在");
        }
        // 设置房间信息
        $data = $this->redis->hGetAll($table_id);
        if(empty($data['user_now'])){
            $this->json("没有用户。");
        }
        $user_now = $data['user_now']+1;
        $users_old = json_decode($data['users'],true);
        if(in_array($user_id,$users_old)){
            $this->json("用户已添加。");
        }
        $users = array_merge($users_old,[$user_id]);
        if($user_now > $data['user_num']){
            $this->json("用户数已满，请重新开始牌局。");
        }
        // 修改房间用户信息
        $this->redis->hSet($table_id,"user_now",$user_now);
        $this->redis->hSet($table_id,"users",json_encode($users));
        $this->redis->hSet($table_id,"step","false");// 是否开始
        $this->redis->hSet($table_id,"step_sequence","0");// 本局走的步数
        $this->redis->hSet($table_id,"step_log_0","0");// 本局走地步数及用户
        // 修改用户信息
        $this->redis->hSet($user_id,"table_id",$table_id);
        // 返回最新信息
        $data = $this->redis->hGetAll($table_id);
        $this->json(['status'=>"添加成功",$data]);
    }

    /**
     * 获取房间信息
     * @author 金
     * @create time 2019-9-27 0027 13:57
     */
    public function get_table(){
        $token = input("table_id");
        $data = $this->redis->hGetAll($token);
        $this->json($data);
    }

    public function delete_table(){
        $table_id = input("table_id");
        $this->redis->delete($table_id);
//        $this->redis->hDel($table_id,"users");// 删除哈希数据中的某个键内容
        $this->json("删除成功");
    }

    /**
     * 设置用户
     * 用户属性：
     *      用户名
     *      地图地址
     *      金额
     *      持有卡片
     *      抵押物品
     * @author 金
     * @create time 2019-9-27 0027 11:14
     */
    public function set_user_name(){
        $token = input("token");
        $user_name = input("name");
        $this->redis->hSet($token,"name",$user_name);
        $this->json("success");
    }

    /**
     * 设置地图
     * @author 金
     * @create time 2019-9-27 0027 11:12
     */
    public function get_user_info(){
        $token = input("token");
        $user = $this->redis->hGetAll($token);
        if(empty($user)){
            $this->json("没有用户信息");
        }
        $this->json($user);
    }

    /**
     * 获取设置好的地区信息
     * @author 金
     * @create time 2019-9-27 0027 15:35
     * @return mixed
     */
    private function get_house_info(){
        $path = dirname(__FILE__)."/map/set_info.txt";
        $file = fopen($path,"r+");
        $info = fread($file,filesize($path));
        fclose($file);
        return json_decode($info,true);
    }

    /**
     * 掷色子
     * @author 金
     * @create time 2019-9-27 0027 11:10
     */
    private function get_step(){
        return random_int(1,12);
    }

    /**
     * 设计地图
     * @author 金
     * @create time 2019-9-27 0027 16:18
     * @param $user_num
     * @return array
     * @throws \Exception
     */
    private function get_map($user_num){
        $house = $this->get_house_info();
        $len = count($house);
        // 设置地图信息
        $map = [];
        for($i = 0;$i< $user_num*10;$i++){
            $map[$i] = [];
            if($i%7 == 0 && $i != 0 && $i%5 != 0){
                if($i/7 == 1 || $i/7 == 6){
                    $map[$i]['name'] = "机会";
                }elseif($i/7 == 2 || $i/7 == 7){
                    $map[$i]['name'] = "前进3";
                }else{
                    $map[$i]['name'] = "命运";
                }
            }
            if($i%5 == 0 && $i%2 != 0){
                $map[$i]['name'] = "车站";
                $map[$i]['price'] = 1500;
                $map[$i]['pledge'] = 750;
                $map[$i]['rates'] = 50;
                $map[$i]['one_rates'] = 350;
                $map[$i]['two_rates'] = 1000;
                $map[$i]['three_rates'] = 2000;
                $map[$i]['four_rates'] = 5000;
            }
            if($i%30 == 0 && $i != 0){
                $map[$i]['name'] = "坐牢";
            }
            if($i == 0){
                $map[$i]['name'] = "起点";
            }
            if($i == 10){
                $map[10] = "路过/牢房";
            }
            if($i == 8){
                $map[$i]['name'] = "电站";
                $map[$i]['price'] = 1500;
                $map[$i]['pledge'] = 750;
                $map[$i]['rates'] = 50;
                $map[$i]['one_rates'] = 350;
                $map[$i]['two_rates'] = 1000;
            }
            if($i == 28){
                $map[$i]['name'] = "水站";
                $map[$i]['price'] = 1500;
                $map[$i]['pledge'] = 750;
                $map[$i]['rates'] = 50;
                $map[$i]['one_rates'] = 350;
                $map[$i]['two_rates'] = 1000;
            }
            if($i == 48){
                $map[$i]['name'] = "气站";
                $map[$i]['price'] = 1500;
                $map[$i]['pledge'] = 750;
                $map[$i]['rates'] = 50;
                $map[$i]['one_rates'] = 350;
                $map[$i]['two_rates'] = 1000;
            }
            if($i == 3){
                $map[$i]['name'] = "交税2000";
            }
            if($i == $user_num*10-3){
                $map[$i]['name'] = "交税1000";
            }
            if(empty($map[$i])){
                do{
                    $ttb = random_int(0,$len);
                    if(isset($house[$ttb])){
                        $map[$i] = $house[$ttb];
                        unset($house[$ttb]);
                        $flag = false;
                    }else{
                        $flag = true;
                    }
                }while($flag);
            }
        }
        return $map;
    }

    /**
     * 验证用户是否登录
     * 返回用户编号
     * @author 金
     * @create time 2019-9-27 0027 13:47
     */
    private function login_status(){
        $token = input("token");
        $user = $this->redis->hGet($token,"name");
        if(empty($token) || empty($user)){
            $this->json("用户不存在");
        }
        return $token;
    }

    /**
     * 机会
     * @author 金
     * @create time 2019-9-27 0027 10:59
     */
    private function get_opportunity(){
        $res = [
            0=>"退9格",
            1=>"出狱卡",
            2=>"得到500",
            3=>"罚款300",
            4=>"给钱最少的200",
            5=>"罚款200",
            6=>"得到300",
            7=>"罚款800",
            8=>"房子最少的建一栋",
            9=>"房子最多的拆一栋",
            10=>"停一次",
            11=>"得到650",
            12=>"再掷一次",
            13=>"罚款200",
            14=>"钱最多的人罚款2000",
            15=>"交税1000",
            16=>"得到950",
            17=>"给每个人500",
            18=>"给每个人50",
        ];
        return $res[random_int(0,18)];
    }

    /**
     * 命运/运气
     * @author 金
     * @create time 2019-9-27 0027 10:59
     */
    private function get_fortune(){
        $res = [
            0=>"停一次",
            1=>"出狱卡",
            2=>"得到500",
            3=>"罚款300",
            4=>"回到起点并得2000",
            5=>"得到800",
            6=>"得到900",
            7=>"罚款800",
            8=>"房子最少的建一栋",
            9=>"房子最多的拆一栋",
            10=>"停一次",
            11=>"前进5次",
            12=>"坐牢",
            13=>"罚款200",
            14=>"钱最多的人罚款2000",
            15=>"交税1000",
            16=>"得到1500",
            17=>"每个人给抽卡人500",
            18=>"每个人给抽卡人50",
        ];
        return $res[random_int(0,18)];
    }


    // *********************************************  备用方法 ********************************************************


    private function get_house_info_tmp(){
//        $map = [];// 13/41/42/51/52/53
//        $exp = ["北京市","天津市","上海市","重庆市"];
//        $province = ["石家庄市","邯郸市","洛阳市","开封市","武汉市","成都市",
//            "攀枝花市","贵阳市","遵义市","昆明市","玉溪市"];
//        $path = dirname(__FILE__)."/map/city.txt";
//        $file = fopen($path,"r+");
//        $info = fread($file,filesize($path));
//        fclose($file);
//        $city = explode("-",$info);
//        $k = 0;
//        foreach ($city as $a){
//            $price = 800;
//            if($k%3 == 0){
//                $price = 1200;
//            }
//            if($k%5 == 0){
//                $price = 1500;
//            }
//            if($k%7 == 0){
//                $price = 2000;
//            }
//            if($k%11 == 0){
//                $price = 2600;
//            }
//            if(in_array($a,$exp)){
//                $price = 4000;
//            }
//            if(in_array($a,$province)){
//                $price = 3000;
//            }
//            $map[$k]['name'] = $a;
//            $map[$k]['price'] = $price;
//            $map[$k]['pledge'] = $price/2;
//            $map[$k]['no_house_rates'] = $price*0.05;// price * 0.05
//            $map[$k]['one_house_rates'] = $price;// price * 2^0
//            $map[$k]['two_house_rates'] = $price*2;// price * 2^1
//            $map[$k]['three_house_rates'] = $price*4;// price * 2^2
//            $map[$k]['build_house'] = $price;// price
//            $k++;
//        }
//        $output = json_encode($map,JSON_UNESCAPED_UNICODE);
//        $path1 = dirname(__FILE__)."/map/set_info.txt";
//        $file1 = fopen($path1,"w+");
//        fwrite($file1,$output);
//        fclose($file1);
    }


}