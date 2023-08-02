<?php
error_reporting(E_ERROR | E_PARSE); // Error/Exception engine, always use E_ALL
ini_set('ignore_repeated_errors', TRUE); // always use TRUE
ini_set('display_errors', FALSE); // Error/Exception display, use FALSE only in production environment or real server. Use TRUE in development environment
ini_set('log_errors', TRUE); // Error/Exception file logging engine.
ini_set('error_log', 'errors.log'); // Logging file path
//
require_once('db.php');
require_once('config.php');
require_once("zarinpal_function.php");
require_once('html_p.php');
date_default_timezone_set("Iran");

//***** درایافت اتوریتی از زرین پال
$authority = $_GET['Authority'];

//***** متصل شدن به دیتابیس
$mydb = db_connect();
if($mydb['status']==false){
	send_tel("sendMessage" ,['chat_id'=>admin_id ,'text'=>"در فایل zarincallback_verify.php\nنتونستم به دیتابیس متصل بشم\n".$mydb['detail']]);
	callback_show_html($authority ,false);
	return;
}
$mydb = $mydb['detail'];

//***** خواندن اطلاعات تراکنش از دیتابیس
$trans_read = db_findOne($mydb ,'transaction' ,"*" ,"authority LIKE '%".$authority."%'");
if($trans_read['status']==false){
	send_tel("sendMessage" ,['chat_id'=>admin_id ,'text'=>"در فایل zarincallback_verify.php\nنتونستم اطلاعات رو از دیتابیس transaction بخونم\n".$trans_read['detail']]);
	callback_show_html($authority ,false);
	return;
}
$trans_read        = $trans_read['detail'];
if($trans_read == []){
	send_tel("sendMessage" ,['chat_id'=>admin_id ,'text'=>"در فایل zarincallback_verify.php\nتراکنشی با اتوریتی ".$authority." یافت نشد"]);
	callback_show_html($authority ,false);
	return;
}


//****چک کن ببین قبلا این تراکنش انجام شده؟
if($trans_read['status'] != 1){
    callback_show_html($trans_read['id'] ,true);
    return;
}

//***** چک کردن معتبر بودن تراکنش از جانب زرین پال
$zarinpal_array = ['merchant_id' => zarinpal_merchantID,
				   'amount'		 => $trans_read['main_price'],
				   'authority'   => $_GET['Authority']];
$zp 	= new zarinpal();
$result = $zp->verify($zarinpal_array);

if(isset($result["Status"]) && (($result["Status"] == 100) || ($result["Status"] == 101))){
	//--- اپدیت اطلاعات تراکنش در دیتابیس transaction
	$update_db = db_update($mydb ,'transaction' ,"id=".$trans_read['id'] ,"status=2 ,trans_id='".$result["RefID"]."' ,price='".$result['Amount']."'");
	if($update_db['status']==false){
		send_tel("sendMessage" ,['chat_id'=>admin_id ,'text'=>"در فایل zarincallback_verify.php\nنتونستم اطلاعات دیتابیس transaction با ایدی ".$trans_read['id']." رو اپدیت کنم\n".$update_db['detail']]);
		callback_show_html($trans_read['id'] ,false);
		return;
	}
	
	//--- اپدیت تعداد توکن ههای کاربر
	$update_db = db_update($mydb ,'users' ,"user_id='".$trans_read['user_id']."'" ,"token=`token`+".(unserialize(token_packs)[$trans_read['pack']]['value']) );
	if($update_db['status']==false){
		send_tel("sendMessage" ,['chat_id'=>admin_id ,'text'=>"در فایل zarincallback_verify.php\nنتونستم توکن های کاربر با شناسه".$trans_read['user_id']." رو اپدیت کنم\n".$update_db['detail']]);
		callback_show_html($trans_read['id'] ,false);
		return;
	}
	
	//--- 
	$trans_alert = "#تراکنش_موفق\n";
	$trans_alert .= "📅 <b>تاریخ پرداخت</b>:\n> ".$trans_read['date']."\n";
	$trans_alert .= "👤 <b>نام پرداخت کننده</b>: <b><a href='".(($trans_read['username']=='')?'':"https://t.me/".$trans_read['username'])."'>".$trans_read['name']."</a></b>\n";
	$trans_alert .= "🆔 <b>شناسه پرداخت کننده</b>: <code>".$trans_read['user_id']."</code>\n";
	$trans_alert .= "💰 <b>قیمت قابلیت</b>: ".number_format($trans_read['main_price'])." تومان\n";
	$trans_alert .= "💵 <b>مبلغ پرداخت شده</b>: ".number_format($result['Amount'])." تومان\n";
	$trans_alert .= "🏷️ <b>کد درگاه پرداخت</b>: ".$result["RefID"]."\n";
	$trans_alert .= "🔍 <b>کد پیگیری</b>: <code>".$trans_read['id']."</code>\n";
	$trans_alert .= "\n🟡 <b>بسته خریداری شده</b>:\n";
	$trans_alert .= "💎".unserialize(token_packs)[$trans_read['pack']]['value']."توکن\n";
	//--- اطلاع به ادمین
	send_tel("sendMessage" ,['chat_id'=>admin_id ,'text'=>$trans_alert ,"parse_mode" => "html" ,'reply_markup'=> json_encode(["inline_keyboard" => array(array(array("text"=>"🏠برگشت به منو🏠" ,"callback_data"=>"/start")))]) ]);
	//--- اطلاع به کاربر
	send_tel("sendMessage" ,['chat_id'=>$trans_read['user_id'] ,'text'=>trim($trans_alert)."\n- - - - - - - - -\nاز خرید شما متشکریم🌺\n<b>✍️ در صورت داشتن هرگونه مشکلی به پشتیبانی پیام دهید</b>" ,"parse_mode" => "html" ,'reply_markup'=> json_encode(["inline_keyboard" => array(array(array("text"=>"🏠برگشت به منو🏠" ,"callback_data"=>"/start")))]) ]);
	
	callback_show_html($trans_read['id'] ,true);
	
}else{
	// چک کن ببین ایا کاربر دکمه لغو پرداخت رو زده؟
	if( ($result["Status"] != -21) && ($result["Status"] != -51)  && ($result["Status"] != -11) && ($result["Status"] != 0) ){
		send_tel("sendMessage" ,['chat_id'=>admin_id ,'text'=>"در فایل zarincallback_verify.php\nپاسخ زرین پال ناموفق بود: ".$result["Status"]]);
    }
	callback_show_html($trans_read['id'] ,false);
}