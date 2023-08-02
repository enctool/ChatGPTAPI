

# ChatGPTAPI
A strong api for communication with chatgpt and all kinds of artificial intelligence systems such as photo production, text to voice and...
# این API چیکار میکنه؟
شما بوسیله این API میتونید به انواع سیستم های هوش مصنوعی از جمله ChatGPT متصل شید!!

# امکانات

 - متصل شدن به سیستم پاسخگویی ChatGPT خود سایت اوپن ای‌آی اصلی (نامحدود)
 - دارای انواع حالت های مختلف پاسخگویی ChatGPT مثله (حالت برنامه نویسی,خشن,افسرده,گفتوگوی انسانی و...)
 - قابلیت تولید انواع عکس براساس متن ارسالی شما (متن به عکس)
 - قابلیت تولید انواع عکس با جلوه های (انیمه, نقاشی, midjourney , علمی-تخیلی, فانتزی و...)
 - قابلیت تبدیل متن به گفتار (با اکثر زبان های دنیا از جمله زبان فارسی)
 - قابلیت ست کردن احساساتی مثله (ناراحت,خشن و...) روی صدای تولید شده
 - دارای تنظیمات ست کردن سرعت و... در صدای تولید شده
 - قابلیت ارسال پاسخ های ChatGPT بصورت گفتار (ویس)
 - **تمامی امکانات نامحدود هستند**

# ساخت ربات تلگرامی متصل به این API

در اینجا کد یک ربات تلگرام متصل به این API رو واستون قرار دادیم که با مطالعه pdf زیر میتونید در کمتر از 5 دقیقه یک ربات تلگرامی متصل به ChatGPT بسازید!!

👉 https://github.com/enctool/ChatGPTAPI/blob/main/help_build_botTelegram.pdf

# مستندات راهنمای ارتباط با این API با هر زبان برنامه نویسی ای!
شما میتوانید طبق مستندات زیر به راحتی به این API متصل بشید و سیستم خودتون (اپ گوشی,وبسایت,ربات تلگرام,سیسام های iot,دستگاه های هوشمند و...) به این api متصل کنید

🟡 ***نقشه راه کلی***

شما  در  دو  مرحله  میتوانید  با  API  مربوط  به  ChatGPT  ارتباط  برقرار  کنید  و  پاسخ  خود  را  دریافت  کنید.

***مرحله  اول***) در  مرحله  اول  شما  پرسش  خود  را  به  سمت  API  ارسال  میکنید  و  در  جواب  یک  ID  دریافت  خواهید  کرد

***مرحله  دوم***) سپس  هر  زمانکه  که  جواب  پرسشتان  توسط  ChatGPT  آماده  شد, بصورت  خودکار  پاسخ  برای  شما  ارسال  میشود  و  شما  بوسیله  ID  که  در  مرحله  اول  دریافت  کرده  بودید  میتوانید  تشخیص  دهید  که  پاسخ  دریافت  شده  در  این  مرحله  مربوط  به  کدام  پرسش  است

🔵 ***راهنما درخواست پاسخ متنی***

در ***`مرحله اول`*** شما می بایست پارامترهای موجود در جدول زیر رو با متد POST به آدرسی که مشخص شده ارسال کنید

https://polha.ir/chatgpt_api/chatgpt_api.php
| شرح | نوع | پارامتر |
|    :---:     |     :---:      |     :---:     |
|کد اختصاصی شما| String - اجباری|api|
|صفحه ای که پاسخ ها به آنجا ارسال میشوند| String - اجباری|callback|
|text|String - اجباری|which|
|حالت پرسش|String - اجباری|mode|
|پرسش|String - اجباری|text|
|آرایه ای از عملیات اضافی|String - اجباری|works|

 - توضیح api
	 - برای دریافت api به ربات https://t.me/ChatGPT_source_bot تلگرامی  مراجعه کنید
 - توضیح callback
	 - زمانیکه پاسخ پرسش شما توسط ChatGPT آماده شد, پاسخ به این آدرس ارسال میشود
 - توضیح which
	 - تعیین نوع پرسش که در این حالت باید برابر (text) باشد
 - توضیح mode
	 - میتونید حالت پاسخگویی ربات رو به موارد زیر تغییر دهید (مثلا در حالت ناراحتی و یا برنامه نویسی پاسخ بده)
	 - برای دریافت پاسخ عادی و قابل اطمینان مقدار mode رو خالی بگذارید
	 - در mode=chat: ربات مثله یک انسان پاسخ میدهد
	 - انواع مدها: comedy ,upset ,depress ,love ,poem ,shy ,coward ,impolite ,rough ,programmer ,chat
 - توضیح text
	 - متن پرسش خود رو بنویسید
 - توضیح works
	 - اگه میخواهید پاسخ ارسال شده از سمت ChatGPT بصورت voice باشد کافیه یک آرایه بسازید و عبارت (txtTOvoice) رو در آن قرار دهید

 ```php
$parameters = [
		"api"      => "zxPSIsImQyIjobfbdiZkTdfvdf0esdcsZ4UzlQbHVY",
		"callback" => 'https://example.ir/chatgpt_callback_url.php',
		"which"    => 'text',
		"text"     => 'سلام خوبی؟',
		];
$options = array(
		CURLOPT_URL => 'https://polha.ir/chatgpt_api/chatgpt_api.php',
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => json_encode($parameters),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYHOST => 2,
		CURLOPT_SSL_VERIFYPEER => true
		);
$handle = curl_init();
curl_setopt_array($handle, $options);
$result = curl_exec($handle);
curl_close($handle);
$get_result = json_decode($result ,1);
if($get_result['status'] != true){
	echo $get_result[‘code'].':'.$get_result[‘detail'];
}else{
//  ذخیره مقدار $get_api['id'] در دیتابیس		
}
```

