<?php

class PublicController extends BaseController {
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
	//(已验证)POST修改密码http://localhost/book-laravel/public/public/passChange   12108413/12108413/123/123
	public function passChange(){
		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata,true);
		$IsJson="";
		Tool::is_json($postdata)?$IsJson=true:$IsJson=false;
		$arr=array('userId','oldPass','newPass','renewPass');
		$arr=Tool::getPost($request,$IsJson,$arr);
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
			!Tool::identity('user','user_id',$xuserId,'user_password',$xoldPass)?Tool::error('verify_error'):static::pass_change($xuserId,$xnewPass);
		}
	}
	//(已验证)GET点赞http://localhost/book-laravel/public/public/like/1/12108413/12108413
	public function setLike($xbookKind,$xuserId,$xpassword){
		if(Tool::identity('booklike','user_id',$xuserId,'book_kind',$xbookKind)){
			Tool::error('like_error');
		}
		else{
			$xpassword=Tool::setSecret($xpassword);
			!Tool::identity('user','user_id',$xuserId,'user_password',$xpassword)?Tool::error('verify_error'):static::set_like($xbookKind,$xuserId);
		}
	}
	//(已验证)GET书本总数http://localhost/book-laravel/public/public/bookSum/0/1/12108413
	public function getBookSum($IsAdmin,$type,$xuserId){//IsAdmin决定身份，type决定搜索类型
		if(!$IsAdmin){//用户
			if($type==1){//已被借(自己)
				$sumType=1;
			}else if($type==0){//总数
				$sumType=2;
			}else return Tool::error('');
		}else{//管理员
			if($type==1){//已被借
				$sumType=3;
			}else if($type==0){//总数
				$sumType=4;
			}else return Tool::error('');
		}
		static::get_book_sum($sumType,$xuserId);
	}
	//(已验证)GET显示图书详细http://localhost/book-laravel/public/public/detail/2
	public function bookDetail($xbookKind){
		static::book_detail($xbookKind);
	}
	//(已验证)GET最近添加的图书http://localhost/book-laravel/public/public/recentAdd/12108413
	public function recentAdd($xuserId){
		!Tool::identity('user','user_id',$xuserId,'','')?Tool::error('verify_error'):static::recent_add($xuserId);
	}
	//(已验证)GET获取批次信息http://localhost/book-laravel/public/public/batch
	public function getBatch(){
		static::get_batch();
	}
	//用于搜索与获取图书列表(kind)
	/*"book_kind":书本kind,
	"book_detail_url":书本详细url,
	"book_name":书本名称,
	"book_author":书本作者,
	"book_status":书本状态,
	"favour":点赞数,
	"book_pic":图书图片,
	"isLike":是否被赞*/
	public static function to_search($userId,$type,$keyword){
		$booklist=BookList::whereIn('book_status',array('已被借','未被借'))->paginate(pagesize);
		$book=array();
		foreach ($booklist as $key => $value) {
			$sum=BookList::where('book_kind','=',$booklist[$key]->book_kind)->count();
			$rent=BookList::where('book_kind','=',$booklist[$key]->book_kind)->where('book_status','=','已被借')->count();
			$book_status="共有".$sum."本,已被借".$rent."本";
			$book_detail_url=url."/public/detail/".$booklist[$key]->book_kind;
			$isLike=BookLike::where('user_id','=',$userId)->where('book_kind','=',$booklist[$key]->book_kind)->count();
			$book[$key]=array('book_kind'=>(string)$booklist[$key]->book_kind,
							  'book_detail_url'=>$book_detail_url,
							  'book_name'=>$booklist[$key]->bookbasic->book_name,
							  'book_author'=>$booklist[$key]->bookbasic->book_author,
							  'book_status'=>$book_status,
							  'favour'=>(string)$booklist[$key]->bookbasic->favour,
							  'book_pic'=>$booklist[$key]->bookbasic->book_pic,
							  'isLike'=>(string)$isLike);
		}
		$response = json_encode($book);
		echo $response;
		/*if($keyword==''){//获取列表
			if(!$IsAdmin){//用户
				$where=" where book_status in ('已被借','未被借') ";
			}
			else{//管理员
				$where=" where book_status not in ('未购买')";
			}
		}
		else{//搜索
			if(!$IsAdmin){//用户
				$type=='id'?
				$where=" where book_status in ('已被借','未被借') and li.id = '$keyword' ":
				$where=" where book_status in ('已被借','未被借') and $type like '%$keyword%' ";
			}
			else{//管理员
				$type=='id'?
				$where=" where book_status not in ('未购买') and li.id = '$keyword' ":
				$where=" where book_status not in ('未购买') and $type like '%$keyword%' ";
			}
		}
		if($offset>=0){
			$turn=" LIMIT $page_size OFFSET $offset ";
		}*/
	}
	/**
	//----------private----------
	**/
	private static function pass_change($userId,$password){
		$user=User::where('user_id','=',$userId)->first();
		//var_dump($user->user_password);
		$user->user_password=$password;
		!$user->save()?Tool::error(''):Tool::found();
	}
	private static function set_like($bookKind,$userId){
		$bookbasic=BookBasic::where('id','=',$bookKind)->first();
		//var_dump($book->favour);
		$bookbasic->favour++;
		$booklike=new BookLike;
		$booklike->user_id=$userId;
		$booklike->book_kind=$bookKind;
		if(!$book->save()){
			Tool::error('');
		}else{
			!$booklike->save()?Tool::error(''):Tool::found();
		}
	}
	/*{"sum":"num"}*/
	//获取批次信息
	private static function get_book_sum($sumType,$userId){
		switch ($sumType) {
			case 1:
				$count=BookCirculate::where('user_id','=',$userId)->count();//用户已借阅
				break;
			case 2:
				$count=BookList::whereIn('book_status',array('已被借','未被借'))->count();//用户总数
				break;
			case 3:
				$count=BookList::where('book_status','=','已被借')->count();//管理员查看已借阅
				break;
			case 4:
				$count=BookList::count();//booklist中所有书数 管理员总数
				break;
			default:
				return Tool::error('');
				break;
		}
		$response = array('sum'=>(string)$count);
		$response = json_encode($response);
		echo $response;
	}
	//根据bookKind返回书本详细
	/*{"book_detail": { "book_name": ,
				        "book_author": ,
				        "book_pub": ,
				        "book_type": ,
				        "book_edit": ,
				        "book_price": ,
				        "book_pic": ,
				        "book_link": ,
				        "book_info": ,
				        "favour": },
    	"book_list": [{ "book_id": ,
			            "book_status":,
			            "user_name":,
			            "created_at":,
			            "return_at":},
	    			  {...},...]}*/
	private static function book_detail($bookKind){
		$bookdetail=BookBasic::where('id','=',$bookKind)->first();
		$booklist=BookCirculate::where('book_kind','=',$bookKind)->where('updated_at','=','0000-00-00 00:00:00')->get();
		$book_detail = array(	'book_name'=>$bookdetail->book_name,
								'book_author'=>$bookdetail->book_author,
								'book_pub'=>$bookdetail->book_pub,
								'book_type'=>$bookdetail->book_type,
								'book_edit'=>$bookdetail->book_edit,
								'book_price'=>(string)$bookdetail->book_price,
								'book_pic'=>$bookdetail->book_pic,
								'book_link'=>$bookdetail->book_link,
								'book_info'=>$bookdetail->book_info,
								'favour'=>(string)$bookdetail->favour);
		$book_list = array();
		foreach ($booklist as $key => $value) {
			$book_id=$booklist[$key]->book_id;
			$book_status=$booklist[$key]->booklist->book_status;
			$user_name=$booklist[$key]->user->user_name;
			$created_at=$booklist[$key]->created_at."";
			$created_at=substr($created_at,0,10);
			$time=strtotime($created_at);
			$timenow=time();   
			//$datesum =31;//需要加减的日期数
			$time1=31*3600*24;  
			//$xuhuan=date('Y-m-d H:i:s',$time+$time1);
			$day=(int)(($time+$time1-$timenow)/3600/24);
			$hour=(int)((($time+$time1-$timenow)/3600/24-$day)*24);
			$min=(int)(((($time+$time1-$timenow)/3600/24-$day)*24-$hour)*60);
			$return_at=$day."天".$hour."时".$min."分";
			$book_list[$key]=array('book_id'=>(string)$book_id,
							 'book_status'=>$book_status,
							 'user_name'=>$user_name,
							 'created_at'=>$created_at,
							 'return_at'=>(string)$return_at);
		}
		$book_detail+=array('book_list'=>$book_list);
		$response = json_encode($book_detail);
		echo $response;
	}
	//返回最近添加的图书
	/*"book_kind":书本kind,
	"book_detail_url":书本详细url,
	"book_name":书本名称,
	"book_author":书本作者,
	"book_status":书本状态,
	"favour":点赞数,
	"book_pic":图书图片,
	"isLike":是否被赞*/
	private static function recent_add($userId){
		$recent=substr(BookList::orderBy('book_time','desc')->lists('book_time')[0],0,10);
		$booklist=BookList::where('book_time','like',$recent."%")->get();
		$recent_add=array();
		foreach ($booklist as $key => $value) {
			$sum=BookList::where('book_kind','=',$booklist[$key]->book_kind)->count();
			$rent=BookList::where('book_kind','=',$booklist[$key]->book_kind)->where('book_status','=','已被借')->count();
			$book_status="共有".$sum."本,已被借".$rent."本";
			$book_detail_url=url."/public/detail/".$booklist[$key]->book_kind;
			$isLike=BookLike::where('user_id','=',$userId)->where('book_kind','=',$booklist[$key]->book_kind)->count();
			$recent_add[$key]=array('book_kind'=>(string)$booklist[$key]->book_kind,
									'book_detail_url'=>$book_detail_url,
									'book_name'=>$booklist[$key]->bookbasic->book_name,
									'book_author'=>$booklist[$key]->bookbasic->book_author,
									'book_status'=>$booklist[$key]->book_status,
									'favour'=>(string)$booklist[$key]->bookbasic->favour,
									'book_pic'=>$booklist[$key]->bookbasic->book_pic,
									'isLike'=>(string)$isLike);
		}
		$response = json_encode($recent_add);
		echo $response;
	}
	/*{"batches":["0","1"]}*/
	//获取批次信息
	private static function get_batch(){
		$batch=BookList::Distinct(true)->lists('act_id');
		$array=array('batches'=>$batch);
		$response = json_encode($array);
		echo $response;
	} 
}