<?php
require('vendor/autoload.php');

use ApiAi\Client;
use ApiAi\Model\Query;
use ApiAi\Method\QueryApi;
//$hubVerifyToken = 'TOKEN123456abcd';
// $accessToken = "EAAYGq9HiIM0BAGipJ83wsWWZBdIETtaEmtDyY81qbI2H7QLj90Ah0Ng2feUidJIewMxpd5O4E5pTIPhWiYQVMEF3qOZA41Ru7BtRZCdnkMtnUiSViJZAJ1wIXF30EOLFCmwyewLiP9iGTZCMBrl4MZBZAToGMk7cvlQQ4IkqvVWVwZDZD";

$hubVerifyToken = getenv('hubVerifyToken');
$accessToken = getenv('accessToken');


$conn = pg_connect("host=ec2-54-243-55-1.compute-1.amazonaws.com port=5432 dbname=dfi5om1rl2d9ev user=cmtddoqynjiyoy password=ee61e2ab338eadd716e5f6f20f0ea3b8c1223b826b9e06557d5aa77a1abe5356 sslmode=require");

// check token at setup
if ($_REQUEST['hub_verify_token'] === $hubVerifyToken) {
  echo $_REQUEST['hub_challenge'];
  exit;
}

//$senderId = "1473360329360719"; himan
//$senderId = "1515521005145148"; yash

echo ("This is a Facebook massenger page ChatBOT: MyDailyQuotes");
// handle bot's anwser
$input = json_decode(file_get_contents('php://input'), true);

$senderId = $input['entry'][0]['messaging'][0]['sender']['id'];
$messageText = $input['entry'][0]['messaging'][0]['message']['text'];
$messageText = trim($messageText);
$messageText = strtolower($messageText);


$url = "https://graph.facebook.com/v2.6/".$senderId."?fields=first_name,last_name,gender&access_token=".$accessToken;
	
	$curl = curl_init();
	curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $url,
	));

	$resp = curl_exec($curl);
	curl_close($curl);
	
	$resp = json_decode($resp);
	echo $resp->first_name;

	$fname = $resp->first_name;
	$lname = $resp->last_name;
	$gender = $resp->gender;


	$query1 = "UPDATE public.user SET fname = '$fname',lname = '$lname',gender = '$gender' WHERE id= '".$senderId."'";
	$result1 = pg_query($conn,$query1);

	if (pg_affected_rows($result1) < 1) { 
	    
	    $query = "INSERT INTO public.user VALUES ('$senderId','$fname','$lname','$gender')";

		$result = pg_query($conn,$query);
	} 
	
	//$answer = "Hey ".$fname."!";

try {
		$client = new Client('00355556bf8045e7aebe2041350a4d51');
		$queryApi = new QueryApi($client);

		$meaning = $queryApi->extractMeaning('Hello', [
			'sessionId' => '1234567890',
			'lang' => 'en',
			]);
		$res = new Query($meaning);
		$res = $res['result']['fulfillment']['messages'][1]['speech'];
		echo "result \n";
		print_r("res : " . $res);
	} catch (\Exception $error) {
		echo $error->getMessage();
		
	}


$answer = "I didn't understand that. Please Ask me 'hi' or 'time' or select a quick reply.";

$query = 'SELECT * FROM public."user" WHERE id = \''.$senderId.'\'';
	$result = pg_query($conn,$query);
	if (!$result) { 
	    
	    $answer = "Not found, Please Ask me 'hi'.";
	} else {
		$row=pg_fetch_assoc($result);
		$fname = trim($row['fname']);
		$lname = trim($row['lname']);
		$subs = trim($row['subscribed']);
	}


if($messageText == "hi" || $messageText == 'yo') {

    $answer = "Hey ".$fname." ".$lname."! ";
 
} 

else if ($messageText == "time") {
	$jsondate = file_get_contents("https://script.googleusercontent.com/macros/echo?user_content_key=MwFNcl0KVozlITfkYtONGeBbBrGl1rnO8t0EIrYYKlsSiwzC-Kh2ogcpvBZxRZUJLgumLvhll4Sl-70MQrllKOt4k-Rnhq50m5_BxDlH2jW0nuo2oDemN9CCS2h10ox_1xSncGQajx_ryfhECjZEnJ9GRkcRevgjTvo8Dc32iw_BLJPcPfRdVKhJT5HNzQuXEeN3QFwl2n0M6ZmO-h7C6bwVq0tbM60-xcVIW3tKXBXruTRuukcZWQ&lib=MwxUjRcLr2qLlnVOLh12wSNkqcO1Ikdrk");
	
	$res = json_decode($jsondate);

	if(!empty($res)) {
		$answer = "Now - Time : " .$res->hours.":".$res->minutes.":".$res->seconds." Date : ".$res->day." / ".$res->month." / ".$res->year;
	} else {
		$answer = "Time is Not Available...";
	}
 	
} 
else if($messageText == 'subscribe'){
	$query = "UPDATE public.user SET subscribed = NOT subscribed WHERE id= '".$senderId."'";
	$result = pg_query($conn,$query);
	if (!$result) { 
	    echo "Problem with query " . $query . "<br/>"; 
	    echo pg_last_error(); 
	    $answer = "Not found,Please Ask me 'hi'.";
	} else {
		$answer = "Thank you for subscribing..";
		$subs = "t";
	}
} 
else if($messageText == 'unsubscribe'){
	$query = "UPDATE public.user SET subscribed = NOT subscribed WHERE id= '".$senderId."'";
	$result = pg_query($conn,$query);
	if (!$result) { 
	    echo "Problem with query " . $query . "<br/>"; 
	    echo pg_last_error(); 
	    $answer = "Not found,Please Ask me 'hi'.";
	} else {
		$answer = "Sad to see you Unsubscribe";
		$subs = "f";
	}

} 
else if($messageText == "send me a quote"){
	// These code snippets use an open-source library.
		// These code snippets use an open-source library.
	$response1 = Unirest\Request::post("https://andruxnet-random-famous-quotes.p.mashape.com/?cat=famous&count=1",
	  array(
	    "X-Mashape-Key" => "RjFaPuxwyPmshu5ioZYL3bmWPMO4p1smZPijsnM5Iq6Ry3BeOk",
	    "Content-Type" => "application/x-www-form-urlencoded",
	    "Accept" => "application/json"
	  )
	);
	print_r($response1->raw_body);                            
	if(!empty($response1)){
		$jsondata = json_decode($response1->raw_body);
		$answer = '\"' . $jsondata->quote . '\"\nAuthor : ' . $jsondata->author ;
	}	
}

else if(substr_compare($messageText, "broadcast", 0, 9) == 0  && $senderId == 1473360329360719){
	$splitmessage = explode("\"", $messageText);
	$banswer = $splitmessage[1];
	
	$query = "SELECT * from public.user WHERE subscribed = 'true'";
	$result = pg_query($conn,$query);
	if (!$result) { 
	    $answer = "Not found, Please Ask me 'hi'.";
	} else {
		$answer = "Broadcast Succesfull!";
		while($row = pg_fetch_assoc($result)){
			$bid = trim($row['id']);
			$bresponse = '{
				"recipient":{
					"id":"'.$bid.'"
				},
				"message":{
					"text":"'.$banswer.'"
				}
			}';

				$bch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token='.$accessToken);
				curl_setopt($bch, CURLOPT_POST, 1);
				curl_setopt($bch, CURLOPT_POSTFIELDS, $bresponse);
				curl_setopt($bch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
				if(!empty($messageText)){
					curl_exec($bch);
				}
				curl_close($bch);
		}

	}
}

else if(substr_compare($messageText, "forcebroadcast", 0, 14) == 0 && $senderId == 1473360329360719){
	$splitmessage = explode("\"", $messageText);
	$banswer = $splitmessage[1];
	
	$query = "SELECT * from public.user";
	$result = pg_query($conn,$query);
	if (!$result) { 
	    $answer = "Not found, Please Ask me 'hi'.";
	} else {
		$answer = "Forced Broadcast Succesfull!";
		while($row = pg_fetch_assoc($result)){
			$bid = trim($row['id']);
			$bresponse = '{
				"recipient":{
					"id":"'.$bid.'"
				},
				"message":{
					"text":"'.$banswer.'"
				}
			}';

				$bch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token='.$accessToken);
				curl_setopt($bch, CURLOPT_POST, 1);
				curl_setopt($bch, CURLOPT_POSTFIELDS, $bresponse);
				curl_setopt($bch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
				if(!empty($messageText)){
					curl_exec($bch);
				}
				curl_close($bch);
		}

	}
}else{
	$answer = "else";
	try {
		$client = new Client('00355556bf8045e7aebe2041350a4d51');
		$queryApi = new QueryApi($client);

		$meaning = $queryApi->extractMeaning('Hello', [
			'sessionId' => '1234567890',
			'lang' => 'en',
			]);
		$res= new Query($meaning);
		$answer = $res['result']['fulfillment']['messages'][0]['speech'];
	} catch (\Exception $error) {
		echo $error->getMessage();
		$answer = "Api.ai error";
	}

}
		

	// $response = [
 //    'recipient' => [ 'id' => $senderId ],
 //    'message' => [ 'text' => $answer]
	// ];



if($subs == 'f'){
	$response = '{
		"recipient":{
			"id":"' . $senderId . '"
		}, 
		"message":{
			"text":"' . $answer . '",
			"quick_replies":[
			{
				"content_type":"text",
				"title":"Send Me A Quote",
				"payload":"DEVELOPER_DEFINED_PAYLOAD_FOR_PICKING_OMQ"
			},
			{
				"content_type":"text",
				"title":"Subscribe",
				"payload":"DEVELOPER_DEFINED_PAYLOAD_FOR_PICKING_SUBSCRIBE"
			}
			]
		}
	}';
}else{
	$response = '{
		"recipient":{
			"id":"' . $senderId . '"
		}, 
		"message":{
			"text":"' . $answer . '",
			"quick_replies":[
			{
				"content_type":"text",
				"title":"Send Me A Quote",
				"payload":"DEVELOPER_DEFINED_PAYLOAD_FOR_PICKING_OMQ"
			}
			]
		}
	}';
}



$ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token='.$accessToken);
// Set some options - we are passing in a useragent too here
curl_setopt($ch, CURLOPT_POST, 1);

//curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
curl_setopt($ch, CURLOPT_POSTFIELDS, $response);

curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
// Send the request & save response to $resp
if(!empty($messageText)){
	curl_exec($ch);
}
// Close request to clear up some resources
curl_close($ch);
