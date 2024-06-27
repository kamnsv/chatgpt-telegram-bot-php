<?php
class tgbot{
  private $token;
  private $apikey;

  // Конструктор
  public function __construct($token, $api)
  {
    $this->token = $token;
    $this->apikey = $api;
    $this->response = '';
  }

  // Функция для выполнения API-запросов
  private function open_url($url, $method, $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    if ($data) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
      ));
    }

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
  }

  // Функция для управления API Telegram
  private function control_api($method, $data = null) {
    $token = $this->token;
    return $this->open_url("https://api.telegram.org/bot$token$method", "POST", $data);
  }

  // Функция для отправки сообщения
  public function send_message($to, $text, $parse_mode = 'md') {
    $data = array(
      'chat_id' => $to,
      'text' => $text,
      'parse_mode' => $parse_mode
    );
    return $this->control_api("/sendMessage", $data);
  }

  // Функция для получения ответа от OpenAI
  public function get_answer($q) {
    // Настройка параметров запроса
    $url = 'https://api.openai.com/v1/chat/completions';

    $data = array(
      'model' => 'gpt-4o',
      'messages' => array(
        array(
          'role' => 'user',
          'content' => $q
        )
      ),
      'temperature' => 0.7,
    );

    // Настройка запроса CURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      "Authorization: Bearer $this->apikey"
    ));

    // Отправка запроса и получение ответа
    $this->response = curl_exec($ch);
    curl_close($ch);
    
    $res = json_decode($this->response, true);
    return isset($res['choices'][0]['message']['content']) ? $res['choices'][0]['message']['content'] : "";
  }
}
