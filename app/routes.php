<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: 'GET'");
header("Access-Control-Allow-Methods: 'POST'");
header("Access-Control-Max-Age: '60'");
define("pagesize", 15, true);
define("saltkey", 'nigoule');
define("url",'http://www.flappyant.com/book/API.php');
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
//最后，app/routes.php 文件会被加载。一旦 routes.php 文件被加载，Request 对象就被发送给应用程序对象，继而被派发到某个路由上。
Route::get('/', 'HomeController@showWelcome');
Route::get('/demo1', 'AdminController@demo1');///demo1?tmp1=1
Route::get('/demo2/{tmp1}', 'AdminController@demo2');
/*Route::get('/',APP::abort(404));*/

Route::get('/getInfo', 'AdminController@getInfo');
/*Route::get('/', 'AdminController@show');*/
ROute::post('/public/passChange','PublicController@passChange');

/*//你也可以在 matched 事件上注册一个监听器，当一个传入请求已经和一个路由相匹配，但还未执行此路由之前，此事件就会被触发：
Route::matched(function($route, $request)
{
    //
});

//接受post方法 demo/post.php


//路由参数 优先级低于正则表达式
Route::get('user/{id}', function($id)
{
    return 'User '.$id.'bai';
});

//可选路由参数 优先级按位置来定
Route::get('user/{name?}', function($name = null)
{
    return $name.'zong';
});

//正则表达式限定的路由参数 
Route::get('user/{name}', function($name)
{
    echo 'hei';
})
->where('name', '[A-Za-z]+');
Route::get('user/{id}', function($id)
{
    echo 'lv';
})
->where('id', '[0-9]+');

//传递参数限定的数组
Route::get('user/{id}/{name}', function($id, $name)
{
    echo 'wozaizheer';
})
->where(array('id' => '[0-9]+', 'name' => '[a-z]+'));

//定义全局模式 优先级优于正则表达式
Route::pattern('id', '[0-9]+');

Route::get('user/{id}', function($id)
{
    // Only called if {id} is numeric.
    return 'huang';
});	

//如果想在路由范围外访问路由参数，可以使用 Route::input 方法：？？？？？？？
Route::filter('foo', function()
{
    if (Route::input('id') == 1)
    {
        echo 'quququ';
    }
});

//定义一个路由过滤器
Route::filter('old', function()
{
    if (Input::get('age') < 200)
    {
        return Redirect::to('home');
    }
});

//为路由绑定过滤器
Route::get('user', array('before' => 'old', function()
{
    return 'You are over 200 years old!';
}));

//将过滤器绑定为控制器Action
Route::get('user', array('before' => 'old', 'uses' => 'UserController@showProfile'));*/