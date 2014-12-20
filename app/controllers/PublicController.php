<?php

class PublicController extends BaseController {
	//（已验证）POST修改密码http://localhost/api/book/public/public/passChange   12108413/12108413/123/123
	public function passChange(){
		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata,true);
		$IsJson="";
		Tool::is_json($postdata)?$IsJson=true:$IsJson=false;
		$arr=array('userId','oldPass','newPass','renewPass');
		$arr=Tool::getPost($request,$IsJson,$arr);
		//var_dump($arr);
		$xuserId = $arr['userId'];
		$xoldPass = $arr['oldPass'];
		$xnewPass = $arr['newPass'];
		$xrenewPass = $arr['renewPass'];
		//var_dump($xuserId.'<br/>'.$xoldPass.'<br/>'.$xnewPass.'<br/>'.$xrenewPass);
		if($xnewPass!=$xrenewPass) 
			Tool::error('password_error');//确定password是否等于repassword
		else{
			$xnewPass=Tool::setSecret($xnewPass);
			$xoldPass=Tool::setSecret($xoldPass);
			!Tool::identity('user','user_id',$xuserId,'user_password',$xoldPass)?Tool::error('verify_error'):static::pass_Change($xuserId,$xnewPass);
		}
	}
	private static function pass_Change($userId,$password){
		$user=User::where('user_id','=',$userId)->find(User::where('user_id','=',$userId)->lists('id')[0]);//take(1)->get();
		//var_dump($user->user_password);
		$user->user_password=$password;
		!$user->save()?Tool::error(''):Tool::found();
	}


}