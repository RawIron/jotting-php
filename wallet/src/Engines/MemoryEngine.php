<?php

namespace RawIron\Wallet\Engines;


class MemoryEngine implements Engine {

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
      $balances = array();
      foreach ($this->_currencies as $currency) {
          $balances[$currency] = $this->_accounts[$currency]['balance'];
      }
      return $balances;
  }

  public function update($userId, $amounts) {
    foreach ($this->_currencies as $currency) {
        $this->_accounts[$currency]['balance'] += $amounts[$currency];
    }
    return true;
  }

}
