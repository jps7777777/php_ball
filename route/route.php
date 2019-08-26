<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\facade\Route;

//Route::get('think', function () {
//    return 'hello,ThinkPHP5!';
//});

Route::get('hello/:name', 'index/hello');
Route::get('game/match', 'index/Game/index');



Route::get('game/update', 'index/Game/update_result');






Route::get('user/info','index/User/get_user_info');
// 用户注册登录
Route::get('register', 'index/Login/register');
Route::get('login', 'index/Login/login_html');
Route::get('inLogin', 'index/Login/login_interface');
Route::get('send/code', 'index/Login/send_verify_code');
// 其他登录方式
Route::get('wx_login', 'index/Wx/wx_login');
Route::get('zfb_login', 'index/Login/zfb_login');
Route::get('qq_login', 'index/Login/qq_login');
Route::get('wb_login', 'index/Login/wb_login');

//return [
//
//];
