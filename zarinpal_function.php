<?php
class zarinpal
{

	public function request($get_data){
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.zarinpal.com/pg/v4/payment/request.json',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS =>json_encode($get_data),
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'Accept: application/json'),
		));

		$result = curl_exec($curl);
		$err 	= curl_error($curl);
		curl_close($curl);
				
		$result = json_decode($result, true);
		
		if($err){
			$Status 		= 0;
			$Message 		= "cURL Error #:" . $err;
			$Authority 		= "";
			$StartPayUrl 	= "";
		}else
		if($result["errors"] != [] && $result["errors"] != ''){
			$Status 		= 0;
			$Message 		= "zarinpal response error: ".$result["errors"]['code']."\n";
			$Message 	   .= $result["errors"]['message']."\n\n";
			foreach($result["errors"]['validations'] as $err){
				$Message .= "> ".json_encode($err)."\n\n";
			}
			$Authority 		= "";
			$StartPayUrl    = "";
		}else{
			$Status 		= (isset($result["data"]['code']) 	&& $result["data"]['code'] != "") ? $result["data"]['code'] : 0;
			$Message 		= $result["data"]['message'];
			$Authority 		= (isset($result["data"]['authority']) 	&& $result["data"]['authority'] != "") 	? $result["data"]['authority'] : "";
			$StartPayUrl    = (isset($result["data"]['authority']) 	&& $result["data"]['authority'] != "") 	? "https://www.zarinpal.com/pg/StartPay/". $Authority : "";
		}
			
		return array(
			"Status" 	=> $Status,
			"Message" 	=> $Message,
			"StartPay" 	=> $StartPayUrl,
			"Authority" => $Authority
		);
	}

	public function verify($get_data){
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.zarinpal.com/pg/v4/payment/verify.json',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS =>json_encode($get_data),
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'Accept: application/json'),
		));

		$result = curl_exec($curl);
		$err 	= curl_error($curl);
		curl_close($curl);
						
		$result = json_decode($result, true);
		
		if($err){
			$Status 		= 0;
			$Message 		= "cURL Error #:" . $err;
			$RefID 			= "";
			$wages          = "";
		}else
		if($result["errors"] != [] && $result["errors"] != ''){
			$Status 		= 0;
			$Message 		= "zarinpal response error: ".$result["errors"]['code']."\n";
			$Message 	   .= $result["errors"]['message']."\n\n";
			foreach($result["errors"]['validations'] as $err){
				$Message .= "> ".json_encode($err)."\n\n";
			}
			$RefID 		    = "";
			$wages          = "";
		}else{
			$Status 		= (isset($result["data"]['code']) 	&& $result["data"]['code'] != "") ? $result["data"]['code'] : 0;
			$Message 		= $result["data"]['message'];
			$RefID    		= $result["data"]['ref_id'];
			$wages          = $result["data"]['wages'];
		}
		
		return array(
			"Status" 	=> $Status,
			"Message" 	=> $Message,
			"Amount" 	=> $get_data['amount'],
			"RefID" 	=> $RefID,
			"Authority" => $get_data['authority'],
			"wages"     => $wages
		);	
	}
}
