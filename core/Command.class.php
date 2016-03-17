<?php

class Command {
  
  /*** SETUP & AUTOLOADING ***/
  public static function setup() {
    global $argv;
    $data = array_merge($argv);
    self::$script = basename(array_shift($data), '.php');
    self::$rawData = $data;
    $jsonData = (object)[];
    foreach($data as $part) {
      $jsonData = (object)array_merge((array)$jsonData, (array)json_decode($part));
    }
    if (isset($jsonData->commandline)) {
      $commandLine = $jsonData->commandline;
      $jsonData->command = $commandLine[0];
      $jsonData->params = array_slice($commandLine, 1);
    } else {
      $jsonData->command = "";
      $jsonData->params = [];
    }
    self::$jsonData = $jsonData;
  }
  
  public static function getScript() {
    return self::$script;
  }
  public static function getRawData() {
    return self::$rawData;
  }
  public static function getJsonData() {
    return self::$jsonData;
  }
  
  public static function __callStatic($name, $argument) {
    if (strpos($name, 'get') === 0) {
      $param = ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', substr($name, 3))), '_');
      if (isset(self::$jsonData->$param)) {
        return self::$jsonData->$param;
      }
    }
    return null;
  }
  
  /*** ATTRIBUTES ***/
  private static $loadPaths;
  private static $script;
  private static $rawData;
  private static $jsonData;
}
Command::setup();