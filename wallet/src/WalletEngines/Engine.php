<?php

namespace RawIron\Wallet\WalletEngines;


interface Engine {
  public function read($userId);
  public function update($userId, $balances);
}

