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
  public static function CodeBlock($message) {
    return '```'.$message.'```';
  }
}