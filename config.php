<?php

//***** 
define('bot_api','***************'); // bot token
define('bot_username','***************'); //یوزرنیم ربات شما بدون @

// سه عددی ادمین ها
define('admin_id', 00000);

// اطلاعات دیتابیس
define('db_name','***************'); 
define('db_username','***************'); 
define('db_pass','***************');

// اطلاعات توکن کاربر
define('token_firstStart' ,10); //زمانیکه فرد برای بار اول ربات رو استارت میکنه چنتا توکن بهش جایزه بدم؟
define('token_giftInvite' ,5); //پاداش فرد دعوت کننده

define('token_left_text' ,2); // هر سوال متنی چند توکن از کاربر کم کنه؟
define('token_left_image' ,5); // هر تولید عکس چند توکن از کاربر کم کنه؟
define('token_left_voice' ,8); // هر تولید ویس چند توکن از کاربر کم کنه؟

define('token_gift_hour' ,12); // بعد از چند ساعت اگه کاربر توکن کافی نداشت بهش توکن بدم؟
define('token_gift_count' ,20); // چه تعداد توکن بهش بدم؟

// بسته های خرید توکن
define('token_packs' ,serialize([
								 '1'=>['price'=>25000 /*price (toman)*/ ,'value'=>100 /*token*/] ,
							     '2'=>['price'=>40000 ,'value'=>200] ,
							     '3'=>['price'=>55000 ,'value'=>300] ,
							     '4'=>['price'=>80000 ,'value'=>500]
							  ]));
							
// اطلاعات مربوط به زرین پال
define('zarinpal_merchantID', '***************'); // مرچنت کد زرین پال شما
define('zarinpal_callback_url', '***************'); // آدرس callback

// 
define('serverChatGPT_apiCode' ,'***************');
define('serverChatGPT_callback' ,'***************');

//----------------------------------------------------------------------------------------------------------------
function send_tel($type ,$parameters){
	return send_request_post('https://api.telegram.org/bot'.bot_api.'/'.$type ,$parameters);
}
function send_request_post($url ,$parameters="" ,$header="") {
    try{
		$params = json_encode($parameters);
		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $parameters,
			CURLOPT_RETURNTRANSFER => true ,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_SSL_VERIFYPEER => true
		);
		if($header != ""){
			array_push($options ,$header);
		}
        $handle = curl_init();
        curl_setopt_array($handle, $options);
        
        $result = curl_exec($handle);
        $status = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);
        
		return $result;
    }catch(Exception $e) {
        return ['send_error'=>$e]; 
    }
}

function startsWith ($string, $startString){
    $len = mb_strlen($startString,'UTF-8');
    return (mb_substr($string, 0, $len ,'UTF-8') === $startString);
}
function endsWith($string, $endString){
    $len = mb_strlen($endString,'UTF-8');
    if ($len == 0) {
        return true;
    }
    return (mb_substr($string, -$len ,'UTF-8') === $endString);
}
?>