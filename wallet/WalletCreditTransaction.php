<?php

namespace RawIron\Wallet;

require_once 'WalletTransaction.php';


/**
 * multiple calls of withdraw() are supported
 * only one currency
 * ensures only reserved amount (or less) by withdraw() calls is commited()
 *
 */


class WalletCreditTransaction extends WalletTransaction {

    public function __construct($session, $currencies, $log) {
        parent::__construct($session, $currencies, $log);
        $this->_reset();
    }
    
    protected function _reset() {
        parent::_reset();
        foreach ($this->getCurrencies() as $currency) {
            $this->_accounts[$currency]['approved'] = false;
        }
    }
    
    
    /**
     * check for sufficient funds
     */   
    private function _approve() {
        $this->_syncBalance();
        
        // sufficient funds?
        foreach ($this->getCurrencies() as $currency) {
            if( ($this->_accounts[$currency]['debits'] <= $this->_accounts[$currency]['balance']) ) {
                $this->_accounts[$currency]['approved'] = true;
            }
        }

        foreach ($this->getCurrencies() as $currency) {
            if( ($this->_accounts[$currency]['approved'] === false) ) {
                return false;
            }
        }
        return true;
    }


    public function sub($amount, $currency) {
        if (!parent::sub($amount, $currency)) {
            return false;
        }
        
        // sufficient funds?
        if( $this->_approve() ) {
            return true;
        }
        return false;
    }    
    

    public function commit() {
        if (! $this->_checkIfOneBalanceChanged()) {
          return true;
        }
        
        foreach ($this->getCurrencies() as $currency) {
          if ($this->getTransactionBalance($currency) < 0) {
            if (!$this->_approve()) {
              return false;
            }
          }
        }

        $this->_updateBalance();

        $this->_reset();
        return true;
    }
    
    public function rollback() {           
        $this->_reset();        
        return false;
    }
}

