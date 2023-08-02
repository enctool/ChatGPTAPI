<?php
error_reporting(E_ERROR | E_PARSE); // Error/Exception engine, always use E_ALL
ini_set('ignore_repeated_errors', TRUE); // always use TRUE
ini_set('display_errors', FALSE); // Error/Exception display, use FALSE only in production environment or real server. Use TRUE in development environment
ini_set('log_errors', TRUE); // Error/Exception file logging engine.
ini_set('error_log', 'errors.log'); // Logging file path
//
date_default_timezone_set("Iran");
//***** اضافه کردن کتابخانه ها
require_once('config.php'); 
require_once('db.php'); 

//***** دریافت پارامترها از سرور
$get_parameters = json_decode(file_get_contents('php://input') ,1);
$get_id     = trim($get_parameters['id']);
$get_status = trim($get_parameters['status']);
$get_code   = trim($get_parameters['code']);
$get_detail = trim($get_parameters['detail']);
$get_text   = trim($get_parameters['text']);
$get_files  = json_decode(trim($get_parameters['files']),1);

if($get_id == '' || $get_status == ''){
	return;
}

//***** متصل شدن به دیتابیس
$mydb = db_connect();
if($mydb['status']==false){
	send_tel("sendMessage" ,['chat_id'=>admin_id ,'text'=>"نتونستم به دیتابیس متصل بشم\n".$mydb['detail']]);
	return;
}
$mydb = $mydb['detail'];

//***** خواندن اطلاعات دیتابیس ai
$ai_db = db_findOne($mydb ,'ai' ,"*" ,"id='".$get_id."'");
if($ai_db['status']==false){
	send_tel("sendMessage" ,['chat_id'=>admin_id ,'text'=>"❌ در دیتابیس ai ایدی ".$get_id." رو پیدا نکردم\n".$ai_db['detail']]);
	return;
}
$ai_db = $ai_db['detail'];
if($ai_db == []){
	return;
}
//***** حذف این ایدی از دیتابیس ai
db_delete($mydb ,'ai' ,"id='".$get_id."'");

//***** خواندن اطلاعات فرد از دیتابیس
$user_db = db_findOne($mydb ,'users' ,"*" ,"user_id='".$ai_db['user_id']."'");
if($user_db['status']==false){
	send_tel("sendMessage" ,['chat_id'=>admin_id ,'text'=>"❌ کاربری با شناسه ".$ai_db['user_id']." رو پیدا نکردم\n".$user_db['detail']]);
	return;
}
$user_db = $user_db['detail'];
if($user_db == []){
	return;
}

//***** چک کن ایا کاربر ادمین هست؟
if($user_db['user_id']!=admin_id && ($get_code!=-1)){
	//***** چه تعداد توکن باید کم بشه؟
	if($ai_db['which'] == 'text'){
		$user_db['token'] = $user_db['token']-token_left_text;
	}else
	if($ai_db['which'] == 'txtTOimage'){
		$user_db['token'] = $user_db['token']-token_left_image;
	}
	if($get_files['txtTOvoice'] != []){
		$user_db['token'] = $user_db['token']-token_left_voice;
	}
	//***** چک کردن تعداد توکن های کاربر
	if($user_db['token']<0){
		send_tel("sendMessage" ,['chat_id'=>$ai_db['user_id'] ,
								 'text'=>"تعداد توکن های شما کافی نیست!\n\n💎 <code>توکن های شما: ".$user_db['token']."</code>",
								 'reply_markup' => json_encode(["inline_keyboard" => array(array(array("text"=>"💎برای افزایش توکن کلیک کنید💎" ,"callback_data"=>"/buy_token")),
																						   array(array("text"=>"💰دریافت توکن رایگان💰" ,"callback_data"=>"/invite")))]),
								 'reply_to_message_id'=>$ai_db['msg_id'],
								 "parse_mode" => "html"
								]);
		return;
	}
	$user_db['token'] = max(0 ,$user_db['token']);
	//***** توکن ها رو از کاربر کم کن
	db_update($mydb ,'users' ,"user_id='".$user_db['user_id']."'" ,"token='".$user_db['token']."' ,last_call='".date("Y:m:d H:i:s")."'");
}

//***** چک کردن پاسخ ارسال شده از سمت سرور
if($get_status == false){
	send_tel("sendMessage" ,['chat_id'=>$ai_db['user_id'] ,
							 'text'=>(($get_code==-1)?"❌ پاسخ یافت نشد. لطفا سوال خودتون رو مجددا ارسال کنید":$get_detail)."\n\n💎 توکن های شما: ".$user_db['token'],
							 'reply_to_message_id'=>$ai_db['msg_id'],
							 'reply_markup' => json_encode(["inline_keyboard" => array(array(array("text"=>"چرا پاسخ ارسال نشده؟(کلیک کنید)" ,"callback_data"=>"chatgpt_timeout_error")))])
							]);
	return;
}

//***** ارسال پاسخ به کاربر در تلگرام
if($ai_db['which'] == 'txtTOimage'){
	$get_images = $get_files['txtTOimage'];
	if(count($get_images) == 1){
		send_tel("sendPhoto" ,['chat_id'=>$ai_db['user_id'] ,'caption'=>"💎 توکن های شما: ".$user_db['token'] ,'photo'=>$get_images[0] ,'reply_to_message_id'=>$ai_db['msg_id']]);
	}else{
		$files = [];
		foreach($get_images as $image){
			array_push($files ,array('type' => 'photo' ,'media' => $image ,'caption'=>'' ) );
		}
		//اضافه کردن کپشن
		$files[count($files)-1] = array_merge(end($files) ,['caption'=>"💎 توکن های شما: ".$user_db['token']]);
		send_tel("sendMediaGroup" ,['chat_id'=>$ai_db['user_id'] ,'media'=>json_encode($files) ,'reply_to_message_id'=>$ai_db['msg_id']]);
	}
}else
if($get_files['txtTOvoice'] != []){
	send_tel("sendAudio" ,['chat_id'=>$ai_db['user_id'] ,
						   'audio'=>$get_files['txtTOvoice'][0],
						   'caption'=>"💎 توکن های شما: ".$user_db['token'],
						   'reply_to_message_id'=>$ai_db['msg_id'],
						   "reply_markup" => json_encode(["inline_keyboard" => array(array(array("text"=>"🔁تغییر حالت" ,"callback_data"=>"/set_textMode"),
																						  array("text"=>"🏠منو" ,"callback_data"=>"/start")),
																					array(array("text"=>"راهنمای ساخت عکس,متن به صدا(کلیک کنید)" ,"callback_data"=>"help_image"))
																					)])
						]);
}else{
	send_tel("sendMessage" ,['chat_id'=>$ai_db['user_id'] ,
							 'text'=>$get_text."\n\n💎 توکن های شما: ".$user_db['token'],
							 'reply_to_message_id'=>$ai_db['msg_id'],
							  "reply_markup" => json_encode(["inline_keyboard" => array(array(array("text"=>"🔁تغییر حالت" ,"callback_data"=>"/set_textMode"),
																							  array("text"=>"🏠منو" ,"callback_data"=>"/start")),
																						array(array("text"=>"راهنمای ساخت عکس,متن به صدا(کلیک کنید)" ,"callback_data"=>"help_image"))
																						)])
						]);
}
?>