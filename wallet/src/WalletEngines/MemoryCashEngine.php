<?php

namespace RawIron\Wallet\WalletEngines;


class MemoryCashEngine implements Engine {

  private $_accounts = array();
  private $_currencies = null;

  public function __construct($currencies) {
    $this->_currencies = $currencies;
    $this->_reset();
  }

  private function _reset() {
    foreach ($this->_currencies as $currency) {
        $this->_accounts[$currency]['balance'] = 0;
    }
  }
  
  public function read($userId) {
    throw new Exception('Not implemented');
  }

  public function update($userId, $amounts) {
    foreach ($this->_currencies as $currency) {
      if ($this->_accounts[$currency]['balance'] + $amounts[$currency] < 0 ) {
        return false;
      }
    }
    foreach ($this->_currencies as $currency) {
      $this->_accounts[$currency]['balance'] += $amounts[$currency];
    }
    return true;
  }

}
