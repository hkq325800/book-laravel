<?php

class AdminController extends BaseController {
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
	//（已验证）POST登录http://localhost/webservice/book/API.php/admin/login   12108238/12108238
	public function toLogin() {
		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata,true);
		$IsJson="";
		Tool::is_json($postdata)?$IsJson=true:$IsJson=false;
		$arr=array('userId','password');
		$arr=Tool::getPost($request,$IsJson,$arr);
		$xuserId = $arr['userId'];
		$xpassword = $arr['password'];
		$xpassword = Tool::setSecret($xpassword);
		!Tool::adminverify($xuserId,$xpassword)?Tool::error(''):Tool::found();
	}
	//(已验证)GET图书搜索http://localhost/book-laravel/public/admin/search/12108238/1/php?page=1
	//(已验证)GET获取图书列表http://localhost/book-laravel/public/admin/search/12108238/5/all?page=1
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
	//（已验证）GET归还图书http://localhost/book-laravel/public/admin/confirm/47/12108238/12108238
	public function toConfirm($xbookId,$xuserId,$xpassword) {
		if(Tool::adminverify($xuserId,$xpassword)){
			static::to_confirm($xbookId);
		}
	}
	//（已验证）GET批次返回该批次的信息http://localhost/book-laravel/public/admin/getId/0/12108238/12108238
	public function getId($xactId,$xuserId,$xpassword) {
		if(Tool::adminverify($xuserId,$xpassword)){
			static::get_id($xactId);
		}
	}
	//（已验证）GET添加图书http://localhost/book-laravel/public/admin/add/9787111358732/移动端/12108238/12108238
	public function toAdd($xbookIsbn,$xbookType,$xuserId,$xpassword){
		if(Tool::adminverify($xuserId,$xpassword)){
			static::to_add($xbookIsbn,$xbookType);
		}
	}
	//（已验证）GET删除图书http://localhost/book-laravel/public/admin/delete/100/12108238/12108238
	public function toDelete($xbookId,$xuserId,$xpassword) {
		if(Tool::adminverify($xuserId,$xpassword)){
			static::to_delete($xbookId);
		}
	}
	//（已验证）GET查看已经借出的图书http://localhost/book-laravel/public/admin/showRe/12108238/12108238?page=1
	public function showRent($xuserId,$xpassword) {
		if(Tool::adminverify($xuserId,$xpassword)){
			static::show_rent();
		}
	}
	/**
	//----------private----------
	**/
	//完成书的return
	private static function to_confirm($bookId){
		//确定书在数据库中状态正确
		if(!Tool::identity('booklist','id',$bookId,'book_status','已被借')){
			Tool::error('bookverify_error');
		}
		else{
			$updated_at = date("Y-m-d H:i:s"); 
			$booklist=BookList::find($bookId);
			$booklist->book_status='未被借';
			$bookcirculate=BookCirculate::where('book_id','=',$bookId)->where('updated_at','=','0000-00-00 00:00:00')->first();
			$bookcirculate->updated_at=$updated_at;
			$newcirculate=new BookCirculate;
			$newcirculate->book_id=$bookId;
			$newcirculate->book_kind=$bookcirculate->book_kind;
			$newcirculate->user_id=0;
			$newcirculate->created_at=$updated_at;
			$newcirculate->updated_at='0000-00-00 00:00:00';
			if(!$booklist->save()){
				Tool::error('');
			}else{
				if(!$bookcirculate->save()){
					Tool::error('');
				}else{
					!$newcirculate->save()?Tool::error(''):Tool::found();
				}
			}
		}
	}
	//返回批次图书信息
	/*[{"book_id":书本id,
		"book_name":书本名称,
		"boou_info":书本介绍},
		{...},...
		}]*/
	private static function get_id($actId){
		$act=BookList::where('act_id','=',$actId)->get();
		$arr=array();
		foreach ($act as $key => $value) {
			$arr[$key]=array('book_id'=>(string)$act[$key]->id,
							 'book_name'=>$act[$key]->bookbasic->book_name,
							 'book_info'=>$act[$key]->bookbasic->book_info);
		}
		$response = json_encode($arr);
		echo $response;
	}
	//关联isbn与id
    private static function to_add($bookIsbn,$bookType){
    	$arr=Tool::isbn2info($bookIsbn);
 		$bookName=$arr['title'];
 		//echo $book_name;
 		$bookAuthor="";
 		foreach ($arr['author'] as $key => $value) {
 			//var_dump($value);
 			$bookAuthor=$bookAuthor.$value;
 		}unset($value);
 		//echo $bookAuthor;
 		$bookPic=$arr['image'];
 		//echo $bookPic;
 		$bookEdit=$arr['publisher'];
 		//echo $bookEdit;
 		$bookPrice=$arr['price'];
 		//echo $bookPrice;
 		$bookPub=$arr['pubdate'];
 		//echo $bookPub;
 		$bookInfo=$arr['summary'];
 		//echo $bookInfo;
 		$bookLink=$arr['alt'];
 		//echo $bookInfo;
 		if(!Tool::identity('bookbasic','book_isbn',$bookIsbn,'','')){//basic中不存在三张表中add
    		static::add_basic($bookIsbn,$bookName,$bookAuthor,$bookType,$bookPic,$bookEdit,$bookPrice,$bookPub,$bookInfo,$bookLink);
    	}
    	else{//basic中存在只在两张表中add
	 		static::add_list_cir($bookIsbn);
    	}
    }
	//bookbasic插入图书数据
	private static function add_basic($bookIsbn,$bookName,$bookAuthor,$bookType,$bookPic,$bookEdit,$bookPrice,$bookPub,$bookInfo,$bookLink){
		$bookbasic=new BookBasic;
		$bookbasic->book_isbn=$bookIsbn;
		$bookbasic->book_name=$bookName;
		$bookbasic->book_author=$bookAuthor;
		$bookbasic->book_type=$bookType;
		$bookbasic->book_pic=$bookPic;
		$bookbasic->book_edit=$bookEdit;
		$bookbasic->book_price=$bookPrice;
		$bookbasic->book_pub=$bookPub;
		$bookbasic->book_info=$bookInfo;
		$bookbasic->book_link=$bookLink;
		!$bookbasic->save()?error(''):add_list_cir($bookIsbn);
	}
	//向booklist中插入数据
	private static function add_list_cir($bookIsbn){
		$buyTime=date("Y-m-d H:i:s");
		//确保书的时间唯一性
		if(Tool::identity('booklist','book_time',$buyTime,'','')){
			$buyTime=date("Y-m-d H:i:s",strtotime("+1 second"));
		}
		$booklist=new BookList;
		$booklist->book_kind=BookBasic::where('book_isbn','=',$bookIsbn)->first()->id;
		$booklist->book_time=$buyTime;
		if(!$booklist->save()){
			Tool::error('');
		}else{
			$bookcirculate=new BookCirculate;
			$bookcirculate->book_id=BookList::where('book_time','=',$buyTime)->where('book_kind','=',$booklist->book_kind)->first()->id;
			$bookcirculate->book_kind=$booklist->book_kind;
			$bookcirculate->user_id=0;
			$bookcirculate->created_at=$buyTime;
			if(!$bookcirculate->save()){
				Tool::error('');
			}else{
				Tool::found();
			}
		}
	}
	//软删除图书
	private static function to_delete($bookId){
		if(Tool::identity('booklist','id',$bookId,'','')==0){
			Tool::error('');
		}
		else{
			$booklist=BookList::find($bookId);$book_kind=$booklist->book_kind;
			$bookbasic=BookBasic::find($book_kind);
			$bookcirculate=BookCirculate::where('book_id','=',$bookId);
			$booklike=BookLike::where('book_kind','=',$book_kind);
			if(!$booklist->delete()){//booklist删除
				Tool::error('');
			}else{
				if(!$bookcirculate->delete()){//bookcirculate删除
					Tool::error('');
				}else{
					if(BookList::where('book_kind','=',$book_kind)->count()==0){//判断为最后一本
						if(!$bookbasic->delete()){//bookbasic删除
							Tool::error('');
						}else{//bookbasic不删除booklike删除
							!$booklike->delete()?Tool::error(''):Tool::found();
						}
					}else{//判断不为最后一本bookbasic不删除booklike删除
							!$booklike->delete()?Tool::error(''):Tool::found();
					}
				}
			}
		}
	}
	//查看已借出的书
	/*"book_id":书本id,
	"book_name":书本名称,
	"book_author":书本作者,
	"user_name":借阅人,
	"favour":点赞数,
	"book_pic":图书图片,
	"created_at":借阅时间,
	"return_at":剩余天数*/
	private static function show_rent(){
		$bookcirculate=BookCirculate::where('user_id','<>',0)->where('updated_at','=',null)->paginate(pagesize);
		$book=array();
		foreach ($bookcirculate as $key => $value) {
			$user_name=$bookcirculate[$key]->user->user_name;//
			$created_at=$bookcirculate[$key]->created_at."";//
			$return_at=Tool::created_at2return_at($created_at);//
			$book[$key]=array('book_id'=>(string)$bookcirculate[$key]->book_id,
							  'book_name'=>$bookcirculate[$key]->bookbasic->book_name,
							  'book_author'=>$bookcirculate[$key]->bookbasic->book_author,
							  'user_name'=>$user_name,
							  'favour'=>(string)$bookcirculate[$key]->bookbasic->favour,
							  'book_pic'=>$bookcirculate[$key]->bookbasic->book_pic,
							  'created_at'=>$created_at,
							  'return_at'=>$return_at);
		}
		$response = json_encode($book);
		echo $response;
	}
}
