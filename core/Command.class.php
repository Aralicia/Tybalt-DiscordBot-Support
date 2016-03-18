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
    
    $jsonData->command = "";
    $jsonData->options = [];
    $jsonData->params = [];
    if (isset($jsonData->commandline)) {
      $commandLine = $jsonData->commandline;
      $jsonData->command = $commandLine[0];
      foreach(array_slice($commandLine, 1) as $entry) {
        if (preg_match('/^--(.+)=(.*)$/', $entry, $matches)) {
          $jsonData->options[$matches[1]] = $matches[2];
        } else if (preg_match('/^--(.+)$/', $entry, $matches)) {
          $jsonData->options[$matches[1]] = true;
        } else if (preg_match('/^-([^-]+)$/', $entry, $matches)) {
          $jsonData->options[] = $matches[1];
          foreach(str_split($matches[1]) as $match) {
            $jsonData->options[$match] = true;
          }
        } else {
          $jsonData->params[] = $entry;
        }
      }
    } else {
      $jsonData->commandline = [];
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
  public static function getOption($keys, $default=false) {
    if (!is_array($keys)) {
      $keys = [$keys];
    }
    foreach($keys as $key) {
      if (isset(self::$jsonData->options[$key])) {
        return self::$jsonData->options[$key];
      }
    }
    return $default;
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
