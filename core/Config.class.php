<?php

class Config {
  
  /*** SETUP ***/
  public static function setup() {
  }
  
  public static function get($name) {
    return json_decode(file_get_contents(dirname(__DIR__).'/config/'.$name.'.json'));
  }
  
  /*** ATTRIBUTES ***/
}
Config::setup();
