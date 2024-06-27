<?php
include "bot.php"; 
include "cfg.php";

$bot= new tgbot($token,$api); //create bot object 

if (isset($_POST['q']) and isset($_POST['s'])) {
  if ($secret != $_POST['s']) exit();
  $text = $_POST['q'];
  $firstname = 'Guest';
  $chat_id = 0;
}
else {
  $data = json_decode(file_get_contents('php://input')); //Get Updates
  $username = $data->message->from->username;
  if (!in_array($username, $usernames)) exit();
  $text = $data->message->text;
  $firstname = $data->message->from->first_name;
  $chat_id = $data->message->from->id;
}

//Checking The Message And Sending Reply
if($text=='/start' and $chat_id != 0){
  $bot->send_message($chat_id,"<b> Hello $firstname Welcome To Ai Bot\n\nthis Bot Created using openai Api","html");
}else{
  if($text != ""){
    $answer=$bot->get_answer($text);
    if ("" == $answer){
      http_response_code(429);
      $answer = $bot->response;
    }
    if ($chat_id != 0) {
      $bot->send_message($chat_id, $answer, 'Markdown');
    }
    else {
      echo $answer;
    }
  }
}
  
?>
