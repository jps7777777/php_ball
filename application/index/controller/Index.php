<?php
namespace app\index\controller;

class Index extends Base
{
    public function index()
    {
        $start_time = microtime();
        $sum = 0;
        for($i = 1;$i<1000000;$i++){
            $sum += $i;
        }
        echo "数据结果：".$sum;
        echo "<br />";
        $end_time = microtime();
        echo "------>".($end_time-$start_time);
        $start_time_two = microtime();
        $sum_two = (1000000+1)*1000000/2;
        echo "第二次计算：".$sum_two;
        echo "<br />";
        $end_time_two = microtime();
        echo "".($end_time_two-$start_time_two);

        return "good idea";
    }

    public function get_sixty_match()
    {
        $match_list = model("Ball")->get_list_by_page('','*','create_time desc',0,60);
        $this->json($match_list);
    }


    public function send_mail(){


//        $mail_content = <<<mailcontent
//    这里是富文本内容区，在此可以写html内容<a href="http://baidu.com">百度</a>
//        mailcontent;
//            $from = base64_encode("邮件标题");
//        $headers = <<<HEADERS
//    From: =?UTF-8?B?{$from}?= <service@example.com>
//    MIME-Version: 1.0
//    Content-Type: text/html; charset="utf-8";
//    HEADERS;

        /*以下是另外一些header参数，按需使用*/
//$headers .= 'Cc: birthdayarchive@example.com' . "\r\n";//抄送
//$headers .= 'Bcc: jqqjj168@163.com' . "\r\n";//暗抄送
//$headers .= "Content-Transfer-Encoding: 8bit\r\n";
        mail();
//        $subject = "=?UTF-8?B?".base64_encode('邮件主题')."?=";
//        mail(substr($email, 0, strpos($email, '@'))." <{$email}>", $subject, $mail_content,$headers);
    }


}
