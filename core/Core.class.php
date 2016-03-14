<?php

class Core {
  
  /*** SETUP & AUTOLOADING ***/
  public static function setup() {
    echo ('Core::setup()');
    self::$loadPaths = ['/', '/models/'];
    spl_autoload_register('Core::autoloadCallback');
  }
  public static function autoloadCallback($class) {
    foreach(self::$loadPaths as $path) {
      $filePath = __DIR__.$path.$class.'.class.php';
      if (file_exists($filePath)) {
        require_once($filePath);
        return;
      }
    }
  }
  
  /*** ATTRIBUTES ***/
  private static $loadPaths;
}
Core::setup();
