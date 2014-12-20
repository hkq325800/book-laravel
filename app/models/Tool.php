<?php

class Tool {
	public static function show() {
		return 'haha';
	}
	//验证管理员身份
	public static function adminverify($xuserId,$xpassword){
		if(!identity('user','user_rank','图书管理','user_id',$xuserId)){
			error('rankverify_error');
		}
		else{
			$xpassword=setSecret($xpassword);
			if(!identity('user','user_id',$xuserId,'user_password',$xpassword)){
				error('verify_error');
			}
			else{
				return 1;
			}
		}
	}
	//验证唯一性
	public static function identity($table,$row1,$value1,$row2,$value2){
		if($row2==''&&$value2==''){
			return $table::where($row1,'=',$value1)->count();
		}else{
			return $table::where($row1,'=',$value1)->where($row2,'=',$value2)->count();
		}
	}
	//获取post数据
	public static function getPost($request,$IsJson,$arr){
		//var_dump($arr);
		//var_dump(Input::get('userId'));
		//var_dump($request['userId']); 
		if($IsJson){//json
			foreach ($arr as $key => $value) {
				$arr[$value]=$request[$value];
			}
			return $arr;
		}
		else{//notjson
			foreach ($arr as $key => $value) {
				$arr[$value]=Input::get($value);
			}
			return $arr;
		}
	}
	//判断post数据是否为json
	public static function is_json($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}
	//向外输出错误信息
	public static function error($type){
		switch ($type) {
			case 'rankverify_error':
				$info="当前用户没有足够权限";
				break;
			case 'bookverify_error':
				$info="书在数据库中状态出错";
				break;
			case 'url_error':
				$info="post中url错误";
				break;
			case 'verify_error':
				$info="提交的用户名/密码错误";
				break;
			case 'password_error':
				$info="两次输入的密码不一致";
				break;
			case 'id_error':
				$info="id重复";
				break;
			case 'sql_error':
				$info="sql语句编写出错";
				break;
			case 'like_error':
				$info="您已赞过此书";
				break;
			case 'id_error':
				$info="此id已存在于数据库中，如需替换请删除原有信息";
				break;
			case 'isbn_error':
				$info="输入的ISBN不匹配";
			default:
				$info="杂七杂八的错误";
				break;
		}
		$response = array('status'=>"0",'info'=>$info);
		$response = json_encode($response);
		echo $response;
	}
	//返回成功信息
	public static function found(){
		$response = array('status'=>"1",'info'=>"成功");
		$response = json_encode($response);
		echo $response;
		//echo '{"status":"1","info":"成功"}';
	}
	//密码处理
	//UPDATE user set user_password=MD5(CONCAT(user_id,'nigoule'))
	//define("saltkey",'',true);
	public static function setSecret($pass){
		return md5( $pass.saltkey,false );
	}
}
