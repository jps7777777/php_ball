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
Route::get('login/login', 'index/Login/login');
Route::get('login/register', 'index/Login/register');
Route::get('login/phone_code', 'index/Login/send_verify_code');

//return [
//
//];
