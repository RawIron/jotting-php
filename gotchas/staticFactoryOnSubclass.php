<?php

class ParentWithFactory
{
  public static function factory() {
    if (get_called_class() == 'ParentWithFactory') {
      print("it is me");
    }
    return new static;
  }

  public function who() {
    print("parent");
  }

  public static function conf() {
    print("conf parent");
  }
}

class Child extends ParentWithFactory
{
  public function who() {
    print("child");
  }

  public static function conf() {
    print("conf child");
  }
}


$instance = ParentWithFactory::factory();
$instance->who();

$instance = Child::factory();
$instance->who();
$instance::conf();
