<?php

class Response {
  
  /*** SETUP & AUTOLOADING ***/
  public static function setup() {
    self::$response_message = [];
    self::$response_private = [];
    self::$response_custom = [];
  }
  
  public static function addMessage($message) {
    self::$response_message[] = $message;
  }
  public static function addPrivate($receiver, $message) {
    self::$response_private[] = (object)['receiver' => $receiver, 'message' => $message];
  }
  public static function addCustomData($key, $data) {
    self::$response_custom[] = (object)['key' => $key, 'data' => $data];
  }
  public static function send() {
    $response = (object)[];
    $response->status = 'ok';
    $response->message = self::$response_message;
    $response->private = self::$response_private;
    
    foreach(self::$response_custom as $data) {
      $key = $data->key;
      $response->$key = $data->data;
    }
    echo (json_encode($response));
    exit();
  }
  
  /*** ATTRIBUTES ***/
  private static $response_message;
  private static $response_private;
  private static $response_custom;
}
Response::setup();