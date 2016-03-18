<?php

class Format {
  
  public static function Bold($message) {
    return '**'.$message.'**';
  }
  public static function Italic($message) {
    return '*'.$message.'*';
  }
  public static function Strikeout($message) {
    return '~~'.$message.'~~';
  }
  public static function Underline($message) {
    return '__'.$message.'__';
  }
  
  public static function Code($message) {
    return '`'.$message.'`';
  }
  public static function CodeBlock($message, $language = "") {
    return '```'.$language.self::NewLine().$message.'```';
  }
  
  public static function UTF8($code) {
    return html_entity_decode('&#'.$code.';');
  }
  public static function NewLine($count=1) {
    $val = '';
    for ($i = 0; $i < $count; $i++) {
      $val .= "\r\n"
    }
    return $val;
  }
}