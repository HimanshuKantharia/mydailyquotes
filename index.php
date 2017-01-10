<?php
require('vendor/autoload.php');

$hubVerifyToken = 'TOKEN123456abcd';
$accessToken = "EAAYGq9HiIM0BAGipJ83wsWWZBdIETtaEmtDyY81qbI2H7QLj90Ah0Ng2feUidJIewMxpd5O4E5pTIPhWiYQVMEF3qOZA41Ru7BtRZCdnkMtnUiSViJZAJ1wIXF30EOLFCmwyewLiP9iGTZCMBrl4MZBZAToGMk7cvlQQ4IkqvVWVwZDZD";

// $accessToken = 'ENV["ACCESS_TOKEN"]';

// check token at setup
if ($_REQUEST['hub_verify_token'] === $hubVerifyToken) {
  echo $_REQUEST['hub_challenge'];
  exit;
}
// handle bot's anwser
$input = json_decode(file_get_contents('php://input'), true);
$senderId = $input['entry'][0]['messaging'][0]['sender']['id'];
$messageText = $input['entry'][0]['messaging'][0]['message']['text'];

$answer = "I don't understand.Please Ask me 'hi'.";

if($messageText == "hi") {


     $answer = "Hello Himanshu, WELCOME!";

} 
else if ($messageText == "time") {
	$result = file_get_contents("http://www.timeapi.org/utc/now?format=%25a%20%25b%20%25d%20%25I:%25M:%25S%20%25Y");

 		if(!empty($result)) {
 			$answer = "Time : " .$result;
 		} else {
 			$answer = "Time is Not Available...";
 		}
}


$response = [
    'recipient' => [ 'id' => $senderId ],
    'message' => [ 'text' => $answer ]
];
$ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token='.$accessToken);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
if(!empty($messageText)){
	curl_exec($ch);
}

curl_close($ch);
