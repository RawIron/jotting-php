<?php

class A
{
  public static $category;

  public function __construct() {
    #self::$category = "ux";
  }

  public static function generateSetters($klass) {
    $me = new ReflectionClass($klass);
    $string = "";
    foreach($me->getProperties() as $property) {
      $property = ucfirst($property->name);
      $string .= <<<CODE

  public function set$property(\$value) {
    \$this->$property = \$value;
    return \$this;
  }

CODE;
    }
    return $string;
  }

  public static final function setCategory() {
  }

  public function show() {
    $me = new ReflectionObject($this);
    foreach($me->getProperties() as $property) {
      print_r($property->name);
      if ($property->isStatic()) {
        print_r(static::${($property->name)});
      } else {
        print_r($this->{$property->name});
      }
      print("\n");
    }
  }

  public function set($data) {
   $me = new ReflectionClass($this);
   foreach($data as $key => $value) {
     if ($me->hasProperty($key) && $me->getProperty($key)->isStatic()) {
        self::${$key} = $value;
     } else {
        $this->{$key} = $value;
     }
   }
  }
}


class B extends A
{
  public $type = "hall";
  public static $category;

  public static function setCategory() {
    print("override static func\n");
  }

  public function a() {
    $prop = "type";
    return $this->${prop};
  }
}


$b = new B();
#print($b->a());
$b->set(array('category' => 'yeah'));
$b->show();
#print($b::generateSetters('B'));
$b->setCategory();

$a = new A();
$a->set(array('type' => 'nono'));
$a->show();

