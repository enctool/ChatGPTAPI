


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

# پشتیبانی
هر سوالی داشتید به پشتیبانی داخل ربات زیر پیام بدید

👉 https://t.me/ChatGPT_source_bot

# ساخت ربات تلگرامی متصل به این API

در اینجا کد یک ربات تلگرام متصل به این API رو واستون قرار دادیم که با مطالعه pdf زیر میتونید در کمتر از 5 دقیقه یک ربات تلگرامی متصل به ChatGPT بسازید!!

👉 https://github.com/enctool/ChatGPTAPI/blob/main/help_build_botTelegram.pdf

# مستندات راهنمای ارتباط با این API با هر زبان برنامه نویسی ای!
شما میتوانید طبق مستندات زیر به راحتی به این API متصل بشید و سیستم خودتون (اپ گوشی,وبسایت,ربات تلگرام,سیسام های iot,دستگاه های هوشمند و...) به این api متصل کنید

🟡 ***نقشه راه کلی***

شما  در  دو  مرحله  میتوانید  با  API  مربوط  به  ChatGPT  ارتباط  برقرار  کنید  و  پاسخ  خود  را  دریافت  کنید.

***مرحله  اول***) در  مرحله  اول  شما  پرسش  خود  را  به  سمت  API  ارسال  میکنید  و  در  جواب  یک  ID  دریافت  خواهید  کرد

***مرحله  دوم***) سپس  هر  زمانکه  که  جواب  پرسشتان  توسط  ChatGPT  آماده  شد, بصورت  خودکار  پاسخ  برای  شما  ارسال  میشود  و  شما  بوسیله  ID  که  در  مرحله  اول  دریافت  کرده  بودید  میتوانید  تشخیص  دهید  که  پاسخ  دریافت  شده  در  این  مرحله  مربوط  به  کدام  پرسش  است

## راهنما درخواست پاسخ متنی

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

 - توضیح api:
   - برای دریافت api به ربات https://t.me/ChatGPT_source_bot تلگرامی  مراجعه کنید
 - توضیح callback:
	 - زمانیکه پاسخ پرسش شما توسط ChatGPT آماده شد, پاسخ به این آدرس ارسال میشود
 - توضیح which:
	 - تعیین نوع پرسش که در این حالت باید برابر (text) باشد
 - توضیح mode:
	 - میتونید حالت پاسخگویی ربات رو به موارد زیر تغییر دهید (مثلا در حالت ناراحتی و یا برنامه نویسی پاسخ بده)
	 - برای دریافت پاسخ عادی و قابل اطمینان مقدار mode رو خالی بگذارید
	 - در mode=chat: ربات مثله یک انسان پاسخ میدهد
	 - انواع مدها: comedy ,upset ,depress ,love ,poem ,shy ,coward ,impolite ,rough ,programmer ,chat
 - توضیح text:
	 - متن پرسش خود رو بنویسید
 - توضیح works:
	 - اگه میخواهید پاسخ ارسال شده از سمت ChatGPT بصورت voice باشد کافیه یک آرایه بسازید و عبارت (txtTOvoice) رو در آن قرار دهید, در غیراینصورت این مقدار را خالی بگذارید

 زمانیکه درخواست بالا را ارسال کردید, در پاسخ json زیر را دریافت خواهید کرد

 ```php
{
  "status": true,
  "code": 100,
  "detail": "Success",
  "id": "11106",
  "turn": "1"
}
 ```
 - توضیح status:
	 - اگه درخواست شما موفقیت آمیز بوده باشه مقدارش true هست و درغیراینصورت برابر false خواهد بود
 - توضیح code:
	 - اگه درخواست شما موفقیت آمیز بوده باشه مقدارش 100 هست و درغیراینصورت یک عدد منفی خواهد بود (به لیست خطاها مراجعه کنید)
 - توضیح detail:
	 - اگه درخواست شما موفقیت آمیز بوده باشه مقدارش success هست و درغیراینصورت متن خطا خواهد بود
 - توضیح id:
	 - اگه درخواست شما موفقیت آمیز بوده باشه یک id به شما داده میشود که شما باید این id رو در برنامه خود ذخیره کنید (در صورتی برگردانده میشود که درخواست شما موفقیت آمیز بوده باشد)
 - توضیح turn:
	 - اگه درخواست شما موفقیت آمیز بوده باشه درخواست شما در api وارد نوبت میشود و اینکه درخواست شما در چه نوبتی قرار دارد در اینجا نوشته میشود (در صورتی برگردانده میشود که درخواست شما موفقیت آمیز بوده باشد)

نمونه کد ارسال درخواست پاسخ متنی
 ```php
$parameters = [
	"api"      => "********************",
	"callback" => 'https://example.ir/chatgpt_callback_url.php',
	"which"    => 'text',
	"text"     => 'سلام خوبی؟',
	"works"    => json_encode(['txtTOvoice'] ,JSON_UNESCAPED_UNICODE),
   	];
$options = array(
	CURLOPT_URL => 'https://polha.ir/chatgpt_api/chatgpt_api.php',
	CURLOPT_CUSTOMREQUEST => "POST",
	CURLOPT_POSTFIELDS => $parameters,
	CURLOPT_RETURNTRANSFER => true ,
	CURLOPT_SSL_VERIFYHOST => 2, //for ssl
	CURLOPT_SSL_VERIFYPEER => true //for ssl
);
$handle = curl_init();
curl_setopt_array($handle, $options);
$result = curl_exec($handle);
$status = curl_getinfo($handle, CURLINFO_HTTP_CODE);
curl_close($handle);
$get_result = json_decode($result ,1);
if($get_result['status'] != true){
	echo $get_result['code'].':'.$get_result['detail'];
}else{
	//  ذخیره مقدار $get_result['id'] در دیتابیس
}
```

در ***`مرحله دوم`*** شما باید در فایلی که در فیلد callback یی که در (مرحله اول) تعیین کردید منتظر پاسخ ChatGPT باشید. (هر زمانکه پاسخ توسط ChatGPT آماده باشد, این پاسخ به فایل callback شما بصورت خودکار ارسال میشود). این پاسخ شامل موارد زیر هست

| شرح | نوع | پارامتر |
|    ---:     |     :---:      |     :---:     |
|همان id هست که در مرحله اول به شما در پاسخ درخواستتان داده شد|Integer|id|
|اگه درخواست شما موفقیت آمیز بوده باشه مقدارش true هست و درغیراینصورت برابر false خواهد بود|Boolean|status|
|اگه درخواست شما موفقیت آمیز بوده باشه مقدارش 100 هست و درغیراینصورت یک عدد منفی خواهد بود (به لیست خطاها مراجعه کنید)|Integer|code|
|اگه درخواست شما موفقیت آمیز بوده باشه مقدارش Success هست و درغیراینصورت متن خطا خواهد بود|String|detail|
|متن پاسخ ارسال شده از سمت ChatGPT|String|text|
|ليست آدرس url فايل هاي ارسال شده از سمت ChatGPT|JSON|files|
|تعداد توكن مصرف شده براي ارسال اين پاسخ|Integer|token|

نمونه پاسخ ارسال شده
```php
{
  "id": 11115,
  "status": 1,
  "code": 100,
  "detail": "Success",
  "text": "سلام! من یک هوش مصنوعی هستم، بنابراین نمی‌توانم احساس کنم. اما می‌توانم به سوالات شما پاسخ دهم. چگونه می‌توانم به شما کمک کنم؟",
  "files": [],
  "token": 1
}
```
نمونه پاسخ ارسال شده درحالتیکه عبارت (txtTOvoice) در بخش works ست شده باشد
```php
{
  "id": 11116,
  "status": 1,
  "code": 100,
  "detail": "Success",
  "text": "سلام! من یک هوش مصنوعی هستم، بنابراین نمی‌توانم احساس کنم. اما می‌توانم به شما کمک کنم. چگونه می‌توانم به شما کمک کنم؟",
  "files": {
    "txtTOvoice": [
      "https://polha.ir/chatgpt_api/files/tLrhJJzI211691015463.mp3"
    ]
  },
  "token": 4
}
```

## راهنما درخواست تولید عکس

در ***`مرحله اول`*** شما می بایست پارامترهای موجود در جدول زیر رو با متد POST به آدرسی که مشخص شده ارسال کنید

https://polha.ir/chatgpt_api/chatgpt_api.php
| شرح | نوع | پارامتر |
|    :---:     |     :---:      |     :---:     |
|کد اختصاصی شما| String - اجباری|api|
|صفحه ای که پاسخ ها به آنجا ارسال میشوند| String - اجباری|callback|
|txtTOimage|String - اجباری|which|
|حالت پرسش|String - اجباری|mode|
|پرسش|String - اجباری|text|
|تعداد عکس ها|String - اجباری|txtTOimage_count|
|عبارات منفی پرسش|String - اجباری|txtTOimage_nPrompt|

 - توضیح which:
	 - تعیین نوع پرسش که در این حالت باید برابر (txtTOimage) باشد
 - توضیح mode:
	 - نوع عکس خروجی رو میتوانید یکی از موارد زیر قرار دهید (در صورتیکه میخواهید عکس بصورت عادی ساخته شود مقدار mode را خالی قرار دهید). مدل هایی که با رنگ زرد مشخص شده اند کیفیت بالاتری دارند
	 - انواع مدها:paint, game, fantasy, anime ,cyberpunk ,nature ,steampunk ,sci-fi ,space ,creepy ,tattoo ,texture ,anything ,photo ,tarot ,impasto oil ,child drawing ,saladwave ,woolworld ,synthwave ,midjourney ,xmas ,debug, ImageHSIv1
 - توضیح text:
	 - توضيح عكسي كه ميخواهيد براساس آن توليد شود رو بنويسيد
 - توضیح txtTOimage_count:
	 - چند عدد عکس تولید شود؟ حداقل تعداد (1) هست و حداکثر آن بستگی به (mode) انتخاب شده دارد
 - توضیح txtTOimage_nPrompt:
	 - از negative prompts برای بهتر کردن نتیجه استفاده میشود. مثلا عکس تولید شده به جای دو دست, 3 دست دارد. حالا شما در negative prompts میتوانید به هوش مصنوعی بگید که 3 دست تولید نکن

 زمانیکه درخواست بالا را ارسال کردید, در پاسخ json زیر را دریافت خواهید کرد

 ```php
{
  "status": true,
  "code": 100,
  "detail": "Success",
  "id": "11202",
  "turn": 1
}
 ```

نمونه کد ارسال درخواست تولید عکس
 ```php
$parameters = [
	"api"               => "********************",
	"callback"          => 'https://example.ir/chatgpt_callback_url.php',
	"which"             => 'txtTOimage',
	"text"              => 'سگ در باغ',
	"mode"              => "anime",
	"txtTOimage_count"  => 4
   	];
$options = array(
	CURLOPT_URL => 'https://polha.ir/chatgpt_api/chatgpt_api.php',
	CURLOPT_CUSTOMREQUEST => "POST",
	CURLOPT_POSTFIELDS => $parameters,
	CURLOPT_RETURNTRANSFER => true ,
	CURLOPT_SSL_VERIFYHOST => 2, //for ssl
	CURLOPT_SSL_VERIFYPEER => true //for ssl
);
$handle = curl_init();
curl_setopt_array($handle, $options);
$result = curl_exec($handle);
$status = curl_getinfo($handle, CURLINFO_HTTP_CODE);
curl_close($handle);
$get_result = json_decode($result ,1);
if($get_result['status'] != true){
	echo $get_result['code'].':'.$get_result['detail'];
}else{
	//  ذخیره مقدار $get_result['id'] در دیتابیس
}
```

در ***`مرحله دوم`*** شما باید در فایلی که در فیلد callback یی که در (مرحله اول) تعیین کردید منتظر پاسخ ChatGPT باشید. (هر زمانکه پاسخ توسط ChatGPT آماده باشد, این پاسخ به فایل callback شما بصورت خودکار ارسال میشود). این پاسخ شامل موارد زیر هست

| شرح | نوع | پارامتر |
|    ---:     |     :---:      |     :---:     |
|همان id هست که در مرحله اول به شما در پاسخ درخواستتان داده شد|Integer|id|
|اگه درخواست شما موفقیت آمیز بوده باشه مقدارش true هست و درغیراینصورت برابر false خواهد بود|Boolean|status|
|اگه درخواست شما موفقیت آمیز بوده باشه مقدارش 100 هست و درغیراینصورت یک عدد منفی خواهد بود (به لیست خطاها مراجعه کنید)|Integer|code|
|اگه درخواست شما موفقیت آمیز بوده باشه مقدارش Success هست و درغیراینصورت متن خطا خواهد بود|String|detail|
|متن پاسخ ارسال شده از سمت ChatGPT|String|text|
|ليست آدرس url فايل هاي ارسال شده از سمت ChatGPT|JSON|files|
|تعداد توكن مصرف شده براي ارسال اين پاسخ|Integer|token|

نمونه پاسخ ارسال شده
```php
{
  "id": 11204,
  "status": 1,
  "code": 100,
  "detail": "Success",
  "text": "",
  "files": {
    "txtTOimage": [
      "https://polha.ir/chatgpt_api/files/RFeYeSmcxU1691079127.jpg",
      "https://polha.ir/chatgpt_api/files/X5NRjvMGBI1691079128.jpg",
      "https://polha.ir/chatgpt_api/files/FYxljWdwEv1691079129.jpg",
      "https://polha.ir/chatgpt_api/files/NNlsHmB8pz1691079130.jpg"
    ]
  },
  "token": 3
}
```
