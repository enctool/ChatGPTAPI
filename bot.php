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
require_once('telegram.php'); 
require_once('zarinpal_function.php'); 


//***** دریافت داده ها از تلگرام
$telegram = new Telegram(bot_api); 

//***** متصل شدن به دیتابیس
$mydb = db_connect();
if($mydb['status']==false){
	send_tel("sendMessage" ,['chat_id'=>$telegram->ChatID() ,'text'=>"❌ متاسفانه خطایی رخ داد!\n لطفا مجددا بر روی /start کلیک کنید"]);
	return;
}
$mydb = $mydb['detail'];

//***** خواندن اطلاعات فرد از دیتابیس
$user_db = db_findOne($mydb ,'users' ,"*" ,"user_id='".$telegram->UserID()."'");
if($user_db['status']==false){
	send_tel("sendMessage" ,['chat_id'=>$telegram->ChatID() ,'text'=>"❌ متاسفانه خطایی رخ داد!\n لطفا مجددا بر روی /start کلیک کنید"]);
	return;
}
$user_db = $user_db['detail'];
if($user_db == []){
	//اگه فرد داخل دیتابیس وجود نداشت, فرد رو به دیتابیس اضافه کن
	$token = token_firstStart;
	//ایا از طریق لینک دوستش وارد ربات شده؟
	if(substr($telegram->Text() ,0 ,10) == "/start inv"){
		$inviter = substr($telegram->Text() ,10);
		//چک کن ببین ایا فردی که از طریق اون دعوت شده اصلا وجود داره؟
		if(db_findOne($mydb ,'users' ,"id" ,"user_id='".$inviter."'")['detail']['id'] != []){
			db_update($mydb ,'users' ,"user_id='".$inviter."'" ,"token=`token`+".token_giftInvite);
			$invited_by = $inviter;
			$token += token_giftInvite;
			$send_gift_true = "👈 چون از طریق لینک دوستت وارد ربات شدی, (<b>".token_giftInvite."</b>) توکن رایگان گرفتی"."\n\n";
			send_tel("sendMessage" ,['chat_id'=>$inviter ,'text'=>"📣 کاربری به اسم (<b>".$telegram->FirstName()."</b>) بوسیله لینک شما عضو ربات شد"."\n\n💰 در نتیجه شما (<b>".token_giftInvite."</b>) توکن هدیه گرفتید." ,"parse_mode" => "html"]);
		}
	}
	//اضافه کردن فرد جدید به دیتابیس
	$user_db = array(
		"id"         => "NULL",
		"date"       => date("Y:m:d H:i:s"), 
		"user_id"    => $telegram->UserID(), 
		"name"       => $telegram->FirstName()." ".$telegram->LastName(),
		"username"   => $telegram->Username(),
		"invited_by" => $invited_by,
		"msg"        => '',
		"text_mode"  => '',
		"txtTOvoice"  => 0,
		"token"      => intval($token),
		"last_call"  => 0
	);
	$insert_result = db_insertOne($mydb ,'users' ,$user_db);
	if($insert_result['status']==false){
		send_tel("sendMessage" ,['chat_id'=>$telegram->ChatID() ,'text'=>"❌ متاسفانه خطایی رخ داد!\n لطفا مجددا بر روی /start کلیک کنید\n".$insert_result['detail']]);
		return;
	}else{
		send_tel("sendMessage" ,['chat_id'=>$telegram->ChatID() ,'text'=>"خوش اومدید!\n\n".$send_gift_true ,"parse_mode" => "html" ,"reply_markup" => json_encode(["inline_keyboard" => array(array(array("text"=>"🏠برگشت به منو🏠" ,"callback_data"=>"/start")))])]);
		send_tel("sendMessage" ,['chat_id'=>admin_id ,'text'=>"👨#عضو_جدید\nکاربری با مشخصات زیر در ربات عضو شد\nتاریخ عضویت: ".date("Y:m:d H:i:s")."\nنام: ".$telegram->FirstName()." ".$telegram->LastName()."\nنام کاربری: @".$telegram->Username()."\nuser id: ".$telegram->UserID() ,"parse_mode" => "html"]);
	}
}

//***** اگه نام یا یوزرنیم فرد عوض شده, داخل دیتابیس تغییرش بده
if(($telegram->FirstName()." ".$telegram->LastName() != $user_db['name']) && (($telegram->FirstName()." ".$telegram->LastName())!="")){db_update($mydb ,'users' ,"id='".$user_db['id']."'" ,"name='".$telegram->FirstName()." ".$telegram->LastName()."'");}
if($telegram->Username() != $user_db['username']){db_update($mydb ,'users' ,"id='".$user_db['id']."'" ,"username='".$telegram->Username()."'");}

//***** ایا میتونم بهش توکن هدیه بدم؟
if((strtotime(date("Y:m:d H:i:s"))-strtotime($user_db['last_call'])) > (token_gift_hour*60*60)){
	if($user_db['token'] < token_gift_count){
		$user_db['token'] = token_gift_count;
		db_update($mydb ,'users' ,"id='".$user_db['id']."'" ,"token='".token_gift_count."'");
	}
}

//**** حذف سوال های تاریخ گذشته
$read_expire_dates = db_find($mydb ,'ai' ,"*" ,"date<='".date('Y-m-d H:i:s',strtotime('-1 hour'))."'");
if($read_expire_dates['status']){
	foreach($read_expire_dates['detail'] as $expires){
		db_delete($mydb ,'ai' ,"id='".$expires['id']."'");
		send_tel("sendMessage" ,['chat_id'=>$expires['user_id'] ,
								 'text'=>"❌ پاسخ یافت نشد. لطفا سوال خودتون رو مجددا ارسال کنید\n💎 توکن های شما: ".$user_db['token'],
								 'reply_to_message_id'=>$expires['msg_id'],
								 'reply_markup' => json_encode(["inline_keyboard" => array(array(array("text"=>"چرا پاسخ ارسال نشده؟(کلیک کنید)" ,"callback_data"=>"chatgpt_timeout_error")))])
							]);
	}
}
				
				
//***** راهنما
if((substr($telegram->Text() ,0 ,10) == "/start inv") || $telegram->Text()=="start" || $telegram->Text()=="/start" || $telegram->Text()=="help" || $telegram->Text()=="/help"){
	db_update($mydb ,'users' ,"id='".$user_db['id']."'" ,"msg=''");
	send_tel("sendMessage" ,["chat_id" => $telegram->ChatID() ,
							 "text" => "سلام!\n🗣️ هر سوالی دارید در زیر برایم بفرستید تا ChatGPT جواب رو واستون بفرسته!\n\n".
									   "چه قابلیت هایی دارم؟\n".
									   "🔎 پاسخ همه چیو بلدم!\n".
									   "📚 انشاهای دانشگاهی بنویسید\n".
				 					   "💻 کد برنامه نویسی\n".
				  					   "🌐 به هر زبانی دوس داشتید حرف بزنید باهام\n".
									   "🖼 میتونم براساس متن شما واستون عکس بسازم\n\n".
									   "✔ برای ساخت عکس, به اول جمله تون کلمه (<b>عکس</b>) رو اضافه کنید.مثلا:👇\n".
									   "<code>عکس سگ در باغ</code>\n".
									   "✔ برای تبدیل متن به صدا, به اول جمله تون (<b>ویس</b>) رو اضافه کنید.مثلا:👇\n".
									   "<code>ویس سلام این صدای منه</code>\n\n".
									   "🥳 هر ".token_gift_hour." ساعت <b>".token_gift_count." توکن</b> رایگان میگیرید!\n\n".
									   "💎 توکن مصرفی هر سوال متنی: ".token_left_text."\n".
									   "💎 توکن مصرفی تولید هر عکس: ".token_left_image."\n".
									   "💎 توکن مصرفی تولید هر ویس: ".token_left_voice."\n".
									   "💎 <code>توکن های شما: ".$user_db['token']."</code>\n\n"
							  "parse_mode" => "html",
							  "reply_markup" => json_encode(["inline_keyboard" => array(array(array("text"=>"تعیین حالت پاسخگویی" ,"callback_data"=>"/set_textMode")),
																					    array(array("text"=>"💎برای افزایش توکن کلیک کنید💎" ,"callback_data"=>"/buy_token")),
																						array(array("text"=>"💰دریافت توکن رایگان💰" ,"callback_data"=>"/invite")))])
							 ]);
}else
//***** دعوت دوستان
if($telegram->Text()=="/invite"){
	db_update($mydb ,'users' ,"id='".$user_db['id']."'" ,"msg=''");
	send_tel("sendMessage" ,["chat_id" => $telegram->ChatID() ,
							 "text" => "قویترین ربات ChatGPT با قابلیت تولید عکس هم اکنون در اختیار شماست!\nدر ضمن اگه با لینک زیر عضو ربات بشی <b>".token_giftInvite." توکن</b> رایگان میگیری!\nhttp://t.me/".bot_username."?start=inv".$telegram->UserID(),
							 "parse_mode" => "html"
							 ]);
	send_tel("sendMessage" ,["chat_id" => $telegram->ChatID() ,
							 "text" => "پیغام بالا رو برای دوستانتون بفرستید تا به محض start ربات توسط دوستتون , هم به شما و هم به دوستتون (<b>".token_giftInvite." توکن</b>) رایگان تعلق گیرد",
							 "parse_mode" => "html",
							 "reply_markup" => json_encode(["inline_keyboard" => array(array(array("text"=>"🏠برگشت به منو🏠" ,"callback_data"=>"/start")))])
							 ]);
}else
//***** خرید توکن
if($telegram->Text()=="/buy_token"){
	db_update($mydb ,'users' ,"id='".$user_db['id']."'" ,"msg=''");
	$inline_keyboard = [];
	foreach(unserialize(token_packs) as $key=>$pack){
		array_push($inline_keyboard ,array(array("text" => "💎".$pack['value'].'توکن='.($pack['price']/1000).'هزارتومان' ,"callback_data" => "buy_token_pack".$key )));
	}
	array_push($inline_keyboard ,array(array("text" => "🏠برگشت به منو" ,"callback_data" => "/start" )));
	send_tel("sendMessage" ,["chat_id" => $telegram->ChatID() ,
							 "text" => "یکی از بسته های زیر را جهت خرید انتخاب کنید",
							 "parse_mode" => "html",
							 "reply_markup" => json_encode(["inline_keyboard" => $inline_keyboard])
							 ]);
}else
// خرید توکن > خرید بسته
if(substr($telegram->Text() ,0 ,14) == "buy_token_pack"){
	$get_pack = substr($telegram->Text() ,14);
	//--- ساخت آرایه برای زرین پال
	$zarinpal_array = ['merchant_id'=> zarinpal_merchantID,
					   'amount'=> unserialize(token_packs)[$get_pack]['price'],
					   'callback_url'=> zarinpal_callback_url,
					   'description'=> 'خرید اشتراک',
					   'currency'=>"IRT"];
	//--- تولید لینک پرداخت زرین پال
	$zp 	= new zarinpal();
	$result = $zp->request($zarinpal_array);
	if(isset($result["Status"]) && $result["Status"] == 100){
		//--- ذخیره این تراکنش در دیتابیس
		$doc = ["id"         => "NULL",
				"status"     => 1,
			    "date"       => date('Y-m-d H:i:s'),
			    "user_id"    => $telegram->UserID(), 
			    "name"       => $telegram->FirstName()." ".$telegram->LastName(),
			    "username"   => $telegram->Username(),
			    "main_price" => unserialize(token_packs)[$get_pack]['price'],
			    "price"      => 0,
			    "authority"  => $result['StartPay'],
			    "trans_id"   => 0,
			    "pack"       => $get_pack
			   ];
		$insertDB = db_insertOne($mydb ,'transaction' ,$doc);
		if($insertDB['status']==false){
			send_tel("answerCallbackQuery" ,['callback_query_id'=>$telegram->Callback_ID() ,'show_alert'=>true ,'text'=>"❌ متاسفانه خطایی رخ داد!\n\nلطفا مجددا تلاش کنید و یا به پشتیبانی پیام دهید\n".$insertDB['detail']]);
			return;
		}
		//--- ارسال لینک به کاربر برای پرداخت
		send_tel("editMessageText" ,["chat_id" => $telegram->ChatID() ,
									 "message_id" => $telegram->MessageID(),
									 "text" => "برای خرید بسته مورد نظر روی لینک زیر و یا دکمه پایین کلیک کنید\n".
											   "\n⚠️️ <b>اعتبار لینک پایین <u>15 دقیقه</u> است و پس از آن منقضی میشود و باید روی دکمه (برگشت) کلیک کنید و سپس مجددا روی بسته مورد نظر جهت خرید کلیک نمایید</b>\n".
											   "\nلینک پرداخت:\n".$result['StartPay'],
									 "parse_mode" => "html",
									 "reply_markup" => json_encode(["inline_keyboard"=>array(array(array("text" => "👈برای خرید اینجا کلیک کنید👉" ,"url" => $result['StartPay'] )),
																							 array(array("text" => "🔙برگشت" ,"callback_data" => "/buy_token" )))])
								 ]);
		return;
	}
	//--- یعنی ساخت لینک پرداخت ناموفق بوده
	send_tel("answerCallbackQuery" ,['callback_query_id'=>$telegram->Callback_ID() ,'show_alert'=>true ,'text'=>"❌ متاسفانه نتونستم لینک پرداخت رو بسازم!\n\nلطفا مجددا تلاش کنید و یا به پشتیبانی پیام دهید"]);
}else
if(($telegram->Text() == '/set_textMode') || (startsWith($telegram->Text() ,"/set_textMode_"))){
	if(startsWith($telegram->Text() ,"/set_textMode_")){
		if($telegram->Text() == "/set_textMode_voiceEN"){
			$user_db['txtTOvoice'] = !$user_db['txtTOvoice'];
			db_update($mydb ,'users' ,"id='".$user_db['id']."'" ,"txtTOvoice='".$user_db['txtTOvoice']."'");
		}else{
			$user_db['text_mode'] = substr($telegram->Text() ,14);
			db_update($mydb ,'users' ,"id='".$user_db['id']."'" ,"text_mode='".$user_db['text_mode']."'");
		}
		send_tel("answerCallbackQuery" ,['callback_query_id'=>$telegram->Callback_ID() ,'show_alert'=>true ,'text'=>"✅ حالت تغییر یافت\n\nالان سوال تونو در زیر واسم بفرستید!"]);
		$telegram_method = "editMessageText";
	}else{
		$telegram_method = "sendMessage";
	}
	send_tel($telegram_method ,["chat_id" => $telegram->ChatID() ,
							 "text" => "میتونید تعیین کنید که ربات بصورت پیشفرض تو کدوم حالت بهتون پاسخ بده؟\nو\nپاسخی که بهتون میده رو بصورت <b>ویس</b> بفرسته یا نه؟",
							 "parse_mode" => "html",
							 "message_id" => $telegram->MessageID(),
							 "reply_markup" => json_encode(["inline_keyboard" => array(
																					   array(array("text"=>(($user_db['txtTOvoice']==1)?"🎤پاسخ رو بصورت ویس میفرستم(جهت تغییر کلیک کن)":"📝پاسخ رو بصورت متن میفرستم(جهت تغییر کلیک کن)") ,"callback_data"=>"/set_textMode_voiceEN")),
																					   array(array("text"=>"➖➖➖➖➖" ,"callback_data"=>"none")),
																					   array(
																							 array("text"=>(($user_db['text_mode']=='chat')?"✅":"")."گفتوگو" ,"callback_data"=>"/set_textMode_chat"),
																							 array("text"=>(($user_db['text_mode']=='none'||$user_db['text_mode']=='')?"✅":"")."عادی (واقعی)" ,"callback_data"=>"/set_textMode_none")
																							 ),
																					   array(
																							 array("text"=>(($user_db['text_mode']=='comedy')?"✅":"")."کمدین" ,"callback_data"=>"/set_textMode_comedy"),
																							 array("text"=>(($user_db['text_mode']=='upset')?"✅":"")."ناراحت" ,"callback_data"=>"/set_textMode_upset")
																							 ),
																					   array(
																							 array("text"=>(($user_db['text_mode']=='depress')?"✅":"")."افسرده" ,"callback_data"=>"/set_textMode_depress"),
																							 array("text"=>(($user_db['text_mode']=='love')?"✅":"")."عشقولانه" ,"callback_data"=>"/set_textMode_love")
																							 ),
																					   array(
																							 array("text"=>(($user_db['text_mode']=='poem')?"✅":"")."شاعرانه" ,"callback_data"=>"/set_textMode_poem"),
																							 array("text"=>(($user_db['text_mode']=='shy')?"✅":"")."خجالتی" ,"callback_data"=>"/set_textMode_shy")
																							 ),
																					   array(
																							 array("text"=>(($user_db['text_mode']=='impolite')?"✅":"")."بی ادب" ,"callback_data"=>"/set_textMode_impolite"),
																							 array("text"=>(($user_db['text_mode']=='rough')?"✅":"")."خشن" ,"callback_data"=>"/set_textMode_rough")
																							 ),
																					   array(array("text"=>(($user_db['text_mode']=='programmer')?"✅":"")."برنامه نویسی" ,"callback_data"=>"/set_textMode_programmer")),
																					   array(array("text"=>"🏠برگشت به منو🏠" ,"callback_data"=>"/start")),
																					   )])
							 ]);
}else
if($telegram->Text() == 'delete_msg'){
	send_tel("deleteMessage" ,['chat_id'=>$telegram->ChatID() ,'message_id'=>$telegram->MessageID()]);
}else
if($telegram->Text() == 'help_image'){
	send_tel("answerCallbackQuery" ,['callback_query_id'=>$telegram->Callback_ID() ,'show_alert'=>true ,'text'=>"ساخت عکس: به اول جمله کلمه (عکس) رو اضافه کن.مثلا:👇\nعکس سگ در باغ\n\nتبدیل متن به صدا: به اول جمله کلمه (ویس) رو اضافه کن.مثلا:👇\nویس سلام\n\n🥳با زدن دکمه (🔁تغییر حالت) ست کن همیشه با (ویس) پاسخ بدم"]);
}else
//***** پاسخ سرور -1 (تایم اوت)
if($telegram->Text() == "chatgpt_timeout_error"){
	send_tel("sendMessage" ,["chat_id" => $telegram->ChatID() ,
							 "parse_mode" => "html",
							 'text'=>"<b>برای اینکه ChatGPT بتونه بهتون پاسخ بده حتما نکات زیر رو رعایت کنید</b>:\n".
									 "1) پاسخ های بیشتر از 4000 بایت محدود شده و ارسال نمیشه. پس اگه فکر میکنید پاسخ سوالتون خیلی زیاده سعی کنید سوال تون رو بخش بخش کنید و هر بخش رو در پستی جداگانه ارسال کنید\n\n".
									 "2) اگه سوال تون یا متن ارسال تون خیلی مبهم باشه و یا بازه جوابش خیلی بزرگ باشه, ChatGPT پاسختون رو نمیده\nمثلا سوال (<code>مقاله بنویس</code>) چون خیلی مبهمه و بازه وسیعی داره, در نتیجه <b>ممکنه ChatGPT هیچ پاسخی نده</b>\n\n".
									 "3) گاهی اوقات بدلیل شلوغ شدن خود سرورهای ChatGPT و یا خطای داخلی خود سرورهای ChatGPT ممکنه پاسخ ارسال نشه\n<b>پس لطفا چند دقیقه صبر کنید و سپس مجددا سوال تون رو بفرستید</b>\n",
							 'reply_markup' => json_encode(["inline_keyboard" => [
																				  array(array("text"=>"❌بستن" ,"callback_data"=>"delete_msg"),
																						array("text"=>"🏠منو" ,"callback_data"=>"/start"))
														 ]]),
							 'reply_to_message_id'=>$telegram->MessageID()
							]);
}
//***** پرسش از چت جی پی تی
else{
	//--- تعیین نوع پرسش
	if(startsWith($telegram->Text() ,"عکس") || $user_db['msg'] != ''){
		if($user_db['msg'] == '' || (in_array($telegram->Text() ,["none","paint","game","fantasy","anime","cyberpunk","nature","steampunk","sci-fi","space","creepy","tattoo","texture","anything","photo","tarot","impasto oil","child drawing","saladwave","woolworld","synthwave","midjourney","xmas","debug","ImageHSIv1"])==false)){
			// نشون دادن حالت های عکس
			if($user_db['msg'] == ''){
				db_update($mydb ,'users' ,"id='".$user_db['id']."'" ,"msg='".$telegram->Text().",".$telegram->MessageID()."'");
			}
			send_tel("sendMessage" ,['chat_id'=>$telegram->ChatID() ,
									 'text'=>"عکسی که تولید میکنم تو کدوم حالت باشه؟",
									 'reply_markup' => json_encode(["inline_keyboard" => [
																						  array(array("text"=>"عکاسی" ,"callback_data"=>"photo"),
																								array("text"=>"انیمه" ,"callback_data"=>"anime"),
																								array("text"=>"نقاشی" ,"callback_data"=>"paint"),
																								array("text"=>"عجیب" ,"callback_data"=>"creepy")),
																						  array(array("text"=>"علمی تخیلی" ,"callback_data"=>"sci-fi"),
																								array("text"=>"هرچیزی" ,"callback_data"=>"anything"),
																								array("text"=>"فانتزی" ,"callback_data"=>"fantasy"),
																								array("text"=>"پشمی" ,"callback_data"=>"woolworld")),
																						  array(array("text"=>"سالادویو" ,"callback_data"=>"saladwave"),
																								array("text"=>"استیمپانک" ,"callback_data"=>"steampunk"),
																								array("text"=>"بازی" ,"callback_data"=>"game"),
																								array("text"=>"طبیعت" ,"callback_data"=>"nature")),
																						  array(array("text"=>"روغن ایمپاستو" ,"callback_data"=>"impasto oil"),
																								array("text"=>"فضا" ,"callback_data"=>"space"),
																								array("text"=>"تاروت" ,"callback_data"=>"tarot"),
																								array("text"=>"بچه ای" ,"callback_data"=>"child drawing")),
																						  array(array("text"=>"بافت" ,"callback_data"=>"texture"),
																								array("text"=>"سایبرپانک" ,"callback_data"=>"cyberpunk"),
																								array("text"=>"سینتویو" ,"callback_data"=>"synthwave"),
																								array("text"=>"خال کوبی" ,"callback_data"=>"tattoo")),
																						  array(array("text"=>"مد گربه ای" ,"callback_data"=>"none"),
																								array("text"=>"کریسمسی" ,"callback_data"=>"xmas"),
																								array("text"=>"midjourney" ,"callback_data"=>"midjourney")),
																						  array(array("text"=>"مدل HSI کیفیت بالا" ,"callback_data"=>"ImageHSIv1")),
																						  array(array("text"=>"🏠برگشت به منو" ,"callback_data"=>"/start"))
																 ]]),
									 'reply_to_message_id'=>$telegram->MessageID()
									]);
			return;
		}
		send_tel("deleteMessage" ,['chat_id'=>$telegram->ChatID() ,'message_id'=>$telegram->MessageID()]);
		$question         = trim(mb_substr(explode(",",$user_db['msg'])[0] ,3 ,4000 ,'UTF-8'));
		$set_token_left   = token_left_image;
		$which            = 'txtTOimage';
		$mode             = $telegram->Text();
		$MessageID        = explode(",",$user_db['msg'])[1];
		$txtTOimage_count = 10;
	}else
	if(startsWith($telegram->Text() ,"ویس")){
		$question       = trim(mb_substr($telegram->Text() ,3 ,4000 ,'UTF-8'));
		$set_token_left = token_left_voice;
		$which          = "txtTOvoice";
		$mode           = $user_db['text_mode'];
		$MessageID      = $telegram->MessageID();
	}else{
		$question       = $telegram->Text();
		$set_token_left = token_left_text;
		$which          = "text";
		$mode           = $user_db['text_mode'];
		$MessageID      = $telegram->MessageID();
		if($user_db['txtTOvoice'] == 1){
			$works = json_encode(['txtTOvoice'] ,JSON_UNESCAPED_UNICODE);
			$set_token_left = token_left_voice+token_left_text;
		}
	}
	db_update($mydb ,'users' ,"id='".$user_db['id']."'" ,"msg=''");
	
	//--- چک کردن طول سوال کاربر
	if($question == ''){
		send_tel("sendMessage" ,['chat_id'=>$telegram->ChatID() ,'text'=>"🧐 لطفا سوالاتتون رو بصورت متنی واسم بفرستید" ,'reply_to_message_id'=>$MessageID]);
		return;
	}
	if(strlen($question)<7){
		send_tel("sendMessage" ,['chat_id'=>$telegram->ChatID() ,'text'=>"😢 سوال تون خیلی کوتاهه!" ,'reply_to_message_id'=>$MessageID]);
		return;
	}
	if(strlen($question)>2000){
		send_tel("sendMessage" ,['chat_id'=>$telegram->ChatID() ,'text'=>"😢 سوال تون خیلی طولانیه!\nحداکثر طول سوالتون میتونه 2000 بایت باشه" ,'reply_to_message_id'=>$MessageID]);
		return;
	}
	
	//--- چک کردن تعداد توکن های کاربر
	if(($telegram->UserID()!=admin_id) && (($user_db['token']-$set_token_left)<0)){
		// محاسبه اینکه بعد از چند ساعت بهش توکن رایگان داده میشه
		$free_token_time = ( (strtotime($user_db['last_call'])+(token_gift_hour*60*60)) - strtotime(date("Y:m:d H:i:s")) ) /60;
		if($free_token_time>1440){
			$free_token_time = intval(($free_token_time/60)/24)." روز";
		}else
		if($free_token_time>60){
			$free_token_time = intval($free_token_time/60)." ساعت";
		}else{
			$free_token_time = intval($free_token_time)." دقیقه";
		}
		//
		send_tel("sendMessage" ,['chat_id'=>$telegram->ChatID() ,
								 'text'=>"تعداد توکن های شما کافی نیست!\n\n💎 <code>توکن های شما: ".$user_db['token']."</code>\n\n".
										  "⏰ تا ".$free_token_time." دیگه بهت (<b>".token_gift_count." توکن</b>) رایگان میدم",
								 'reply_markup' => json_encode(["inline_keyboard" => array(array(array("text"=>"💎برای افزایش توکن کلیک کنید💎" ,"callback_data"=>"/buy_token")),
																						   array(array("text"=>"💰دریافت توکن رایگان💰" ,"callback_data"=>"/invite")))]),
								 'reply_to_message_id'=>$MessageID,
								 "parse_mode" => "html"
								]);
		return;
	}
	
	//--- محدود کردن تعداد درخواست های کاربر
	$max_question = (($which=='text')?5:3);
	if(db_findOne($mydb ,'ai' ,"COUNT(id)" ,"user_id='".$telegram->UserID()."' AND which='".$which."'")['detail']['COUNT(id)'] >= $max_question){
		send_tel("sendMessage" ,['chat_id'=>$telegram->ChatID() ,'text'=>"😢 شما $max_question تا سوال در صف انتظار داری!\nلطفا صبر کنید تا اونا رو اول واست جواب بدم\n".db_findOne($mydb ,'ai' ,"COUNT(id)" ,"user_id='".$telegram->UserID()."' AND which='".$which."'")['detail']['COUNT(id)'] ,'reply_to_message_id'=>$MessageID]);
		return;
	}
	
	//--- ارسال درخواست به سرور
	$get_api = json_decode(send_request_post('https://polha.ir/chatgpt_api/chatgpt_api.php' ,[
																								"api"              => serverChatGPT_apiCode,
																								"callback"         => serverChatGPT_callback,
																								"which"            => $which,
																								"mode"             => $mode,
																								"text"             => $question,
																								"txtTOimage_count" => $txtTOimage_count,
																								//
																								"works"            => $works
																							  ]) ,1);
	
	//--- چک کردن پاسخ سرور
	if($get_api['status'] != true){
		// توکن ربات تموم شده؟
		if($get_api['code'] == -5){
			send_tel("sendMessage" ,['chat_id'=>admin_id ,'text'=>"❌ سلام ادمین, توکن ربات تموم شده باید به ربات مادر برگردی و توکن خرید کنی!"]);
			send_tel("sendMessage" ,['chat_id'=>$telegram->ChatID() ,'text'=>"❌ متاسفانه خطایی رخ داد!\n لطفا مجددا سوال تون رو بفرستید" ,'reply_to_message_id'=>$MessageID]);
			return;
		}else
		// محدودیت تعداد سوال؟
		if($get_api['code'] == -6){
			send_tel("sendMessage" ,['chat_id'=>$telegram->ChatID() ,'text'=>"❌ الان یکم سرم شلوغه!\nلطفا چند دقیقه بعد دوباره سوالتونو بفرستید" ,'reply_to_message_id'=>$MessageID]);
			return;
		}else{
			send_tel("sendMessage" ,['chat_id'=>$telegram->ChatID() ,'text'=>"❌ متاسفانه خطایی رخ داد!\n لطفا مجددا سوال تون رو بفرستید\n\n".$get_api['code'].")".$get_api['detail'] ,'reply_to_message_id'=>$MessageID]);
			return;
		}
	}
			
	//--- ذخیره اطلاعات در دیتابیس
	$doc = ["id"      => $get_api['id'],
			"date"   => date("Y:m:d H:i:s"), 
			"which"   => $which, 
			"user_id" => $telegram->UserID(), 
			"msg_id"  => $MessageID,
		   ];
	$insertDB = db_insertOne($mydb ,'ai' ,$doc);
	if($insertDB['status']==false){
		send_tel("sendMessage" ,['chat_id'=>$telegram->ChatID() ,'text'=>"❌ متاسفانه خطایی رخ داد!\n لطفا مجددا سوال تون رو بفرستید\n".$insertDB['detail'] ,'reply_to_message_id'=>$MessageID]);
		return;
	}
	
	//--- نشون دادن علامت منتظر باش به کاربر
	if($which == "text"){
		send_tel("sendChatAction" ,['chat_id'=>$telegram->ChatID() ,'action'=>"typing"]);
	}else
	if($which == 'txtTOimage'){
		send_tel("sendMessage" ,['chat_id'=>$telegram->ChatID() ,'text'=>"⌛ در حال ساخت...\nنوبت شما: ".$get_api['turn'] ,'reply_to_message_id'=>$MessageID]);
	}else
	if($which == "txtTOvoice"){
		send_tel("sendChatAction" ,['chat_id'=>$telegram->ChatID() ,'action'=>"upload_audio"]);
	}
}
send_tel('answerCallbackQuery' ,['callback_query_id'=>$telegram->Callback_ID()]);
?>