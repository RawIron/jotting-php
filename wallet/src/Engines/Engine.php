<?php

namespace RawIron\Wallet\Engines;


interface Engine {
  public function read($userId);
  public function update($userId, $balances);
}

