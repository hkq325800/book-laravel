<?php

class UserController extends BaseController {
	/**
	//----------way2name----------
	**/
	/*public function:getBatch
	private function:get_batch
	datasource:$bookbasic
	database:BookBasic
	array:book_basic*/
	
	/**
	//----------public----------
	**/
	//(已验证)POST登录http://localhost/book-laravel/public/normal/login   12108413/12108413
	public function toLogin(){
		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata,true);
		$IsJson="";
		Tool::is_json($postdata)?$IsJson=true:$IsJson=false;
		$arr=array('userId','password');
		$arr=Tool::getPost($request,$IsJson,$arr);
		$xuserId = $arr['userId'];
		$xpassword = $arr['password'];
		$xpassword = Tool::setSecret($xpassword);
		if(!Tool::identity('user','user_id','','')){
			Tool::error('');
		}else{
			!Tool::identity('user','user_id',$xuserId,'user_password',$xpassword)?Tool::error('verify_error'):Tool::found();
		}
	}
	//(已验证)POST注册http://localhost/book-laravel/public/normal/register   12108413/黄可庆/0/0
	public function toRegister(){
		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata,true);
		$IsJson="";
		Tool::is_json($postdata)?$IsJson=true:$IsJson=false;
		$arr=array('userId','userName','password','rePassword');
		$arr=Tool::getPost($request,$IsJson,$arr);
		$xuserId = $arr['userId'];
		$xuserName = $arr['userName'];
		$xpassword = $arr['password'];
		$xrepassword = $arr['rePassword'];
		if($xpassword!=$xrepassword) 
			Tool::error('password_error');//确定password是否等于repassword
		else{
			$xpassword=Tool::setSecret($xpassword);
			Tool::identity('user','user_id',$xuserId,'','')?Tool::error('id_error'):static::register($xuserId,$xuserName,$xpassword);//确定user_id是否重复，重复返回0//未找到且注册成功返回1
		}
	}
	//(已验证)GET扫一扫借书http://localhost/book-laravel/public/normal/borrow/1/12108413/12108413
	public function toBorrow($xbookId,$xuserId,$xpassword) {
		if(Tool::identity('booklist','id',$xbookId,'book_status','已被借')){//先确认书的状态book_status 1为已被借
			Tool::error('bookverify_error');
		}
		else{//再验证扫描人密码成功则借取
			$xpassword=Tool::setSecret($xpassword);
			!Tool::identity('user','user_id',$xuserId,'user_password',$xpassword)?Tool::error('verify_error'):Static::to_borrow($xbookId,$xuserId);
		}
	}
	//(已验证)GET图书搜索http://localhost/book-laravel/public/normal/search/12108413/1/php?page=1
	//(已验证)GET获取图书列表http://localhost/book-laravel/public/normal/search/12108413/5/all?page=1
	public function toSearch($xuserId,$xtype,$xkeyword){
		switch ($xtype) {
			case '1'://书名
				$xtype='book_name';
				break;
			case '2'://book_id
				$xtype='id';
				break;
			case '3'://作者
				$xtype='book_author';
				break;
			case '4'://种类
				$xtype='book_type';
				break;
			case '5'://全部
				$xtype='';
				break;
			default:
				Tool::error('url_error');
				break;
		}
		PublicController::to_search($xuserId,$xtype,$xkeyword);
	}
	//（已验证）GET已借阅http://localhost/book-laravel/public/normal/showRe/12108413/12108413?page=1
	public function showRent($xuserId,$xpassword) {
		$xpassword=Tool::setSecret($xpassword);
		!Tool::identity('user','user_id',$xuserId,'user_password',$xpassword)?Tool::error('verify_error'):static::show_rent($xuserId);
	}
	/**
	//----------private----------
	**/
	//注册
	private static function register($userId,$userName,$password){
		$user = new User;
		$user->user_id=$userId;
		$user->user_name=$userName;
		$user->user_password=$password;
		!$user->save()?Tool::error(''):Tool::found();
	}
	//完成扫一扫借书
	private static function to_borrow($bookId,$userId){
		$updated_at = date("Y-m-d H:i:s");
		$booklist=BookList::find($bookId);
		$booklist->book_status='已被借';
		$bookcirculate=BookCirculate::where('book_id','=',$bookId)->where('updated_at','=','0000-00-00 00:00:00')->first();
		$bookcirculate->user_id=$userId;
		$bookcirculate->created_at=$updated_at;
		if(!$booklist->save()){
			//更新booklist表
			Tool::error('');
		}else{
			//更新bookcirculate
			!$bookcirculate->save()?Tool::error(''):found();
		}
	}
	//查看曾借过的书
	/*"book_id":书本id,
	"book_kind":书本kind,
	"book_name":书本名称,
	"book_author":书本作者,
	"book_status":书本状态,
	"favour":点赞数,
	"book_pic":图书图片,
	"isLike":是否被赞,
	"created_at":借阅时间,
	"return_at":剩余天数*/
	private static function show_rent($userId){
		$bookcirculate=BookCirculate::where('user_id','=',$userId)->get();
		$book=array();
		foreach ($bookcirculate as $key => $value) {
			$bookcirculate[$key]->updated_at==null?$book_status='未还':$book_status='已还';//
			$isLike=BookLike::where('user_id','=',$userId)->where('book_kind','=',$bookcirculate[$key]->book_kind)->count();//
			$created_at=$bookcirculate[$key]->created_at."";//
			$return_at=Tool::created_at2return_at($created_at);//
			$book[$key]=array('book_id'=>(string)$bookcirculate[$key]->book_id,
							  'book_kind'=>(string)$bookcirculate[$key]->book_kind,
							  'book_name'=>$bookcirculate[$key]->bookbasic->book_name,
							  'book_author'=>$bookcirculate[$key]->bookbasic->book_author,
							  'book_status'=>$book_status,
							  'favour'=>(string)$bookcirculate[$key]->bookbasic->favour,
							  'book_pic'=>$bookcirculate[$key]->bookbasic->book_pic,
							  'isLike'=>(string)$isLike,
							  'created_at'=>$created_at,
							  'return_at'=>$return_at);
		}
		$response = json_encode($book);
		echo $response;
	}
}