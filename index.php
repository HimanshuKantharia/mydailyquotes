<?php
// parameters
$hubVerifyToken = 'TOKEN123456abcd';
$accessToken = "EAAYGq9HiIM0BAFyPFce270Lnq8xfT5l1Hzm8wGBOxtpAhVTOkxmfRfp3JwVeUE6D6045lg8wYlFSxrw0VaxGnoXB1o8w344LkxJ7vkgMN7XcVhQ6zL6mkN6IysrnKmwauUzRbjkEVUqWzWVV8CZBfA2SVOrwO6kJlDkrSDgZDZD";

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

$answer = "I don't understand. Ask me 'hi'.";
if($messageText == "hi") {
    $answer = "Hello Himanshu";
}

$response = [
    'recipient' => [ 'id' => $senderId ],
    'message' => [ 'text' => $answer ]
];

$ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token='.$accessToken);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

if(!empty($input['entry'][0]['messaging'][0]['message'])){
$result = curl_exec($ch);
}
$result = curl_exec($ch);
curl_close($ch);