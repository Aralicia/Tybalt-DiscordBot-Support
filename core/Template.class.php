<?php

class Template {
  
  /*** SETUP & AUTOLOADING ***/
  public static function setup() {
    self::$path = getcwd().'/templates/';
  }
  
  public static function load($name, $data) {
    $path = self::$path.''.$name.'.tpl.php';
    if (file_exists($path)) {
      ob_start();
      self::_load($path, $data);
      return ob_get_clean();
    }
    return '';
  }
  private static function _load($_tplpath, $_tpldata) {
    extract($_tpldata, EXTR_SKIP);
    include($_tplpath);
  }
  
  /*** ATTRIBUTES ***/
  private static $path;
}
Template::setup();
