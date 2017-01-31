<?php
require('vendor/autoload.php');

$hubVerifyToken = 'TOKEN123456abcd';
$accessToken = "EAAYGq9HiIM0BAGipJ83wsWWZBdIETtaEmtDyY81qbI2H7QLj90Ah0Ng2feUidJIewMxpd5O4E5pTIPhWiYQVMEF3qOZA41Ru7BtRZCdnkMtnUiSViJZAJ1wIXF30EOLFCmwyewLiP9iGTZCMBrl4MZBZAToGMk7cvlQQ4IkqvVWVwZDZD";

// $teleToken = "311805084:AAGSOoUfWn_hZm1yJHNKQLnqe0s2JTNv9aw";
// $teleURL = "https://api.telegram.org/bot311805084:AAGSOoUfWn_hZm1yJHNKQLnqe0s2JTNv9aw/getMe";

//$dbopts = parse_url("localhost/phpmyadmin");
// $app->register(new Herrera\Pdo\PdoServiceProvider(),
//                array(
//                    'pdo.dsn' => 'pgsql:dbname=svnit;host=localhost;port=5432,'.
//                    'pdo.username' => "root",
//                    'pdo.password' => ""
//                )
// );

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

if($messageText == "hi" || $messageText == 'Hi') {


     $answer = "Hey Himanshu!";

} 
else if ($messageText == "Time" || $messageText == "time") {
	$jsondate = file_get_contents("https://script.googleusercontent.com/macros/echo?user_content_key=MwFNcl0KVozlITfkYtONGeBbBrGl1rnO8t0EIrYYKlsSiwzC-Kh2ogcpvBZxRZUJLgumLvhll4Sl-70MQrllKOt4k-Rnhq50m5_BxDlH2jW0nuo2oDemN9CCS2h10ox_1xSncGQajx_ryfhECjZEnJ9GRkcRevgjTvo8Dc32iw_BLJPcPfRdVKhJT5HNzQuXEeN3QFwl2n0M6ZmO-h7C6bwVq0tbM60-xcVIW3tKXBXruTRuukcZWQ&lib=MwxUjRcLr2qLlnVOLh12wSNkqcO1Ikdrk");
	
	$res = json_decode($jsondate);

 		if(!empty($res)) {
 			$answer = "Now - Time : " .$res->hours.":".$res->minutes.":".$res->seconds." Date : ".$res->day." / ".$res->month." / ".$res->year;
 		} else {
 			$answer = "Time is Not Available...";
 		}
}


$response = [
    'recipient' => [ 'id' => $senderId ],
    'message' => [ 'text' => $answer ]
];

$response1 = [
  "recipient":{
    "id"=> $senderId
  },
  "message":{
    "text":"Pick a color:",
    "quick_replies":[
      {
        "content_type":"text",
        "title":"Red",
        "payload":"DEVELOPER_DEFINED_PAYLOAD_FOR_PICKING_RED"
      },
      {
        "content_type":"text",
        "title":"Green",
        "payload":"DEVELOPER_DEFINED_PAYLOAD_FOR_PICKING_GREEN"
      }
    ]
  }
];

	
$ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token='.$accessToken);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response1));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
if(!empty($messageText)){
	curl_exec($ch);
}
curl_close($ch);
