<?php
namespace app\index\controller;

class Index extends Base
{
    public function index()
    {
        return "good idea";
    }

    public function hello()
    {
        $info = model("Ball")->get_info([['id','eq',555555]],"*");
        var_dump($info);




    }


}
