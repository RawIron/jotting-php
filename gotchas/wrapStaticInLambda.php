<?php

class Logger
{
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance instanceof static === false) {
          self::$instance = new static();
        }
        return self::$instance;
    }

    public static function log($msg) {
      print($msg);
    }
}


class A
{
  private $l = null;

  public function __construct() {
    $this->l = function ($msg) { Logger::log($msg); };
  }

  public function t($msg) {
    $f = $this->l;
    return $f($msg);
  }
}


$a = new A();
print($a->t("yeah"));

