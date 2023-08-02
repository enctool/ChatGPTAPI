<?php


function callback_show_html($authority ,$pay_suc){?>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<style>
			body {
				font-family: Arial, sans-serif;
				background-color: #f3f3f3;
			}
			#container {
				max-width: 600px;
				margin: 0 auto;
				text-align: center;
				padding: 20px;
				background-color: #fff;
				border-radius: 10px;
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
			}
			h1_success {
				font-size: 36px;
				color: #4caf50;
				margin: 0;
			}
			h1_fail {
				font-size: 36px;
				color: #ff0000;
				margin: 0;
			}
			img {
				max-width: 100%;
			}
			.box {
				border: 1px solid #ccc;
				padding: 10px;
				margin: 0 auto;
				width: 80%;
				background-color: #f9f9f9;
				border-radius: 5px;
				box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
			}
			.box p {
				font-size: 24px;
				margin: 0;
				color: #4caf50;
			}
			.box span {
				font-size: 18px;
				font-weight: bold;
				color: #333;
			}
			.button {
				display: inline-block;
				background-color: #4caf50;
				color: #fff;
				font-size: 16px;
				padding: 10px 20px;
				border-radius: 5px;
				text-decoration: none;
				margin-top: 10px;
			}
		</style>
	</head>
	<body>
		<div id="container">
			<h1_success><b><?php if($pay_suc){echo "پرداخت با موفقیت انجام شد";}?></b></h1_success>
			<h1_fail><b><?php if(!$pay_suc){echo "عملیات پرداخت ناموفق بود";}?></b></h1_fail>
			<div class="box">
				<p>شماره پیگیری</p>
				<span><?php echo $authority;?></span>
			</div>
			<?php echo "<a href=\"http://t.me/".bot_username."?start=\" class=\"button\">برگشت به ربات</a>";?>
		</div>
	</body>
</html>
<?php
};

?>