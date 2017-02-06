<?php
require('vendor/autoload.php');

$hubVerifyToken = 'TOKEN123456abcd';
$accessToken = "EAAYGq9HiIM0BAGipJ83wsWWZBdIETtaEmtDyY81qbI2H7QLj90Ah0Ng2feUidJIewMxpd5O4E5pTIPhWiYQVMEF3qOZA41Ru7BtRZCdnkMtnUiSViJZAJ1wIXF30EOLFCmwyewLiP9iGTZCMBrl4MZBZAToGMk7cvlQQ4IkqvVWVwZDZD";


// $dbopts = parse_url("postgres://cmtddoqynjiyoy:ee61e2ab338eadd716e5f6f20f0ea3b8c1223b826b9e06557d5aa77a1abe5356@ec2-54-243-55-1.compute-1.amazonaws.com:5432/dfi5om1rl2d9ev");
// $app->register(new Herrera\Pdo\PdoServiceProvider(),
//                array(
//                    'pdo.dsn' => 'pgsql:dbname=dfi5om1rl2d9ev;host=ec2-54-243-55-1.compute-1.amazonaws.com;port=5432,'.
//                    'pdo.username' => "cmtddoqynjiyoy",
//                    'pdo.password' => "ee61e2ab338eadd716e5f6f20f0ea3b8c1223b826b9e06557d5aa77a1abe5356"
//                )
// );

$conn = pg_connect("host=ec2-54-243-55-1.compute-1.amazonaws.com port=5432 dbname=dfi5om1rl2d9ev user=cmtddoqynjiyoy password=ee61e2ab338eadd716e5f6f20f0ea3b8c1223b826b9e06557d5aa77a1abe5356 sslmode=require");

// check token at setup
if ($_REQUEST['hub_verify_token'] === $hubVerifyToken) {
  echo $_REQUEST['hub_challenge'];
  exit;
}

$senderId = "1473360329360719";
// handle bot's anwser
$input = json_decode(file_get_contents('php://input'), true);
$senderId = $input['entry'][0]['messaging'][0]['sender']['id'];
$messageText = $input['entry'][0]['messaging'][0]['message']['text'];
echo $senderId."<br>";

$senderId = "1473360329360719";
$query = "SELECT * FROM public.'user' WHERE id='1473360329360719';";
$result = pg_query($conn,$query);
if (!$result) { 
    echo "Problem with query " . $query . "<br/>"; 
    echo pg_last_error(); 
    $answer = "I don't understand.Please Ask me 'hi'.";
} else {
	$row=pg_fetch_assoc($result);
	echo $row['username'];
	$answer = "name: ".$row['username'];
}





// $answer = "I don't understand.Please Ask me 'hi'.".$row['username'];

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


	
$ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token='.$accessToken);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
if(!empty($messageText)){
	curl_exec($ch);
}
curl_close($ch);
