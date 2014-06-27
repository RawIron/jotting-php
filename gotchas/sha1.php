<?php

class Sha1Test extends PHPUnit_Framework_TestCase
{

  public function testsha1() {
    $todo = array(
      array("salt" => "", "message" => "",
            "signature" => "fbdb1d1b18aa6c08324b7d64b71fb76370690e1d"),
      array("salt" => "heavy", "message" => "rain",
            "signature" => "7e480ccb19b450094b169f6f3779b8a745b1da5b"),
      array("salt" => "b2", "message" => "a1",
            "signature" => "240d6b81caf2ee8cb4161065095ac681fc0ce44a"),
    );

    foreach($todo as $encode) {
      $signature = hash_hmac('sha1', $encode['message'], $encode['salt'], false);
      $this->assertEquals($signature, $encode['signature']);
    }
  }

}
