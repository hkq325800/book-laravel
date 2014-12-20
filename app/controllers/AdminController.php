<?php

class AdminController extends BaseController {

	/*example*/
	/*public function show()
	{
		//return 'hello';
		//return Input::all();
		return JSON_encode(User::find(1), JSON_UNESCAPED_UNICODE);
	}
	public function haha(){
		return Tool::show();
	}*/
	/*public function ip2position(){
		$curl = curl_init('http://int.dpool.sina.com.cn/iplookup/iplookup.php?ip='.$ip); 
		curl_setopt($curl, CURLOPT_FAILONERROR, true); 
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); //
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //
		$result = curl_exec($curl); 
        curl_close($curl);  
  		//var_dump( compact($result));
  		$response = array('status'=>$result);
		$response = json_encode($response);
		echo ($response);
  		//var_dump($result);
	}*/
	public function demo1(){
		return Input::get('tmp1');
	}
	public function demo2($tmp2){
		return ($tmp2);
	}
	public function showAll()
	{
		return JSON_encode(User::all(), JSON_UNESCAPED_UNICODE);
	}
	public function showWhere()
	{
		return JSON_encode(User::where('account','=','hkq325800')->take(10)->get(), JSON_UNESCAPED_UNICODE);
	}

	//localhost/demo/public/logIn?username=hkq325800&password=hkq93214
	public function logIn(){
		if(User::where('account','=',Input::get('username'))->where('password','=',Input::get('password'))->count()==1){
			echo 'ok';
		}
		else if(User::where('account','=',Input::get('username'))->count()==0){
			echo '用户名不存在';
		}
		else{
			echo '密码不正确';
		}
	}
	public function signUp(){
		$user = new User;
		$user->account=Input::get('username');
		$user->password=Input::get('password');
		$user->save();
		echo 'ok';
	}
	public function getInfo(){
		$info="no";
		$flag=false;
		if(Input::get('username')){
			if(User::where('account','=',Input::get('username'))->count()==0){
				$flag=true;
			}
			else{
				$flag=false;
				$info="该用户已存在";
			}
		}
		if(Input::get('password')){
			$flag=true;
		}
		if(Input::get('captcha')){
			if(Input::get('captcha'))
				$flag=true;
			else{
				$flag=false;
				$info="验证码错误";
			}
		}
		if($flag)
			$info='ok';
		echo $info;
	}
	public function city2weather(){
		$cityid=Cityid::where('cityname','like','%'.Input::get('cityname'))->lists('cityid')[0];
		$result=file_get_contents('http://weatherapi.market.xiaomi.com/wtr-v2/weather?cityId='.$cityid);
		echo $result;
	}
	/*public function ip2position(){
		$ip=Input::get('ip');
		$result=file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?ip='.$ip);
        $arr=array();
        $arr=explode("	", $result);
		$arr[5]=iconv('GBK','UTF-8',$arr[5]);
  		echo $arr[5];
	}*/
}
