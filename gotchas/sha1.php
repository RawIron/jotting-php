<?php

class Sha1Test extends PHPUnit_Framework_TestCase {

  public function test_sha1_hmac() {
    $sha1_hmac = array(
      array("salt" => "b2", "message" => "a1",
            "signature" => "240d6b81caf2ee8cb4161065095ac681fc0ce44a"),
      array("salt" => "", "message" => "",
            "signature" => "fbdb1d1b18aa6c08324b7d64b71fb76370690e1d"),
      array("salt" => "heavy", "message" => "rain",
            "signature" => "7e480ccb19b450094b169f6f3779b8a745b1da5b"),
    );

    foreach($sha1_hmac as $encode) {
      $signature = hash_hmac('sha1', $encode['message'], $encode['salt'], false);
      $this->assertEquals($signature, $encode['signature']);
    }
  }


  public function test_sha1() {
    $sha1 = array(
      array("salt" => "b2", "message" => "a1",
            "signature" => "f29bc91bbdab169fc0c0a326965953d11c7dff83"),
      array("salt" => "", "message" => "",
            "signature" => "da39a3ee5e6b4b0d3255bfef95601890afd80709"),
      array("salt" => "heavy", "message" => "rain",
            "signature" => "fbec17cb2fcbbd1c659b252230b48826fc563788"),
    );

    foreach($sha1 as $encode) {
      $signature = sha1($encode['message']);
      $this->assertEquals($signature, $encode['signature']);
    }
  }

}
