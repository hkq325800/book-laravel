<html>
<head>
  <meta charset="utf-8">

</head>
  <body>
<?php
if($_POST){
    //echo post_to_url("http://localhost/webservice/book/test.php/Administrator/login", $_POST);
} else{
 ?>

<!-- <form action="http://www.flappyant.com/book/API.php/recomusers/login" method="post"> -->
<form action="http://localhost/book-laravel/public/public/passChange" method="post">

<!-- 修改图书信息<br/>
<input type="text" name="bookName" placeholder="bookName" /><br>
<input type="text" name="bookAuthor" placeholder="bookAuthor" /><br>
<input type="text" name="bookPub" placeholder="bookPub" /><br>
<input type="text" name="bookType" placeholder="bookType" /><br>
<input type="text" name="bookEdit" placeholder="bookEdit" /><br>
<input type="text" name="bookPrice" placeholder="bookPrice" /><br>
<input type="text" name="bookStatus" placeholder="bookStatus" /><br>
<input type="text" name="bookPic" placeholder="bookPic" /><br>
<input type="text" name="bookLink" placeholder="bookLink" /><br>
<input type="text" name="bookInfo" placeholder="bookInfo" /><br> -->

修改密码<br/>
<input type="text" name="userId" placeholder="userId" /><br>
<input type="text" name="oldPass" placeholder="oldPass" /><br>
<input type="text" name="newPass" placeholder="newPass" /><br>
<input type="text" name="renewPass" placeholder="renewPass" /><br>

<!-- <input type="text" name="bookstatus" placeholder="bookstatus" /><br> -->
<input type="hidden" name="_METHOD" value="POST" />
<input type="submit" value="A D D" />
</form>
<?php 
}

/*function post_curl($_url, $_data) {
   $mfields = '';
   foreach($_data as $key => $val) { 
      $mfields .= $key . '=' . $val . '&amp;'; 
   }
   rtrim($mfields, '&amp;');
   $pst = curl_init();

   curl_setopt($pst, CURLOPT_URL, $_url);
   curl_setopt($pst, CURLOPT_POST, count($_data));
   curl_setopt($pst, CURLOPT_POSTFIELDS, $mfields);
   curl_setopt($pst, CURLOPT_RETURNTRANSFER, 1);

   $res = curl_exec($pst);

   curl_close($pst);
   return $res;
}*/

?>
</body>
</html>