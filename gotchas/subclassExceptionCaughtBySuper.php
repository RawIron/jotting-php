<?php

class MyException extends Exception {};

class C
{
  public function causeTrouble() {
    throw new MyException("prepare to die");
  }
}

function eat() {
  $trouble = new C();
  try { $trouble->causeTrouble(); } catch (Exception $e) { print("ate it"); }
  print("\n");
}

eat();

