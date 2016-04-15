<?php

namespace RawIron\Wallet;

require_once 'WalletEngines.php';


abstract class WalletTransaction {

    private $_session = false;
    private $_log = false;
    
    private $_currencies = array();
    protected $_accounts = array();
    protected $_currency = '';
    

    public function __construct(WalletStore $session, $currencies, $log) {
        $this->_session = $session;
        $this->_currencies = $currencies;
        $this->_log = $log;
         
        foreach ($this->_currencies as $currency) {
            $this->_accounts[$currency]['sync'] = false;
            $this->_accounts[$currency]['balance'] = 0;
        }
         
        $this->_reset();
    }
    
    protected function _reset() {
        foreach ($this->_currencies as $currency) {
          $this->_accounts[$currency]['debits'] = 0;
          $this->_accounts[$currency]['credits'] = 0;
        }
    }    
    
    protected function _checkandSetCurrency($currency) {
        if (! in_array($currency, $this->_currencies) ) {
            return false;         
        }
        $this->_currency = $currency;
        return true;        
    }
    
    protected function _checkAmount($amount) {
        if ( $amount < 0 ) {           
            return false;
        }
        return true;        
    }    

 
    public function getAccountBalance($currency) {
        $this->_checkandSetCurrency($currency);
        return $this->_accounts[$this->_currency]['balance'];
    }    
    
    public function getTransactionBalance($currency) {
        $this->_checkandSetCurrency($currency);
        return   $this->_accounts[$this->_currency]['credits']
               - $this->_accounts[$this->_currency]['debits'];
    }
    
    public function _checkIfOneBalanceChanged() {
        foreach ($this->_currencies as $currency) {            
            if ($this->getTransactionBalance($currency) != 0) {
                return true;
            }
        }
        return false;     
    }
    
    public function getCurrencies() {
        return $this->_currencies;
    }


    protected function _syncBalance() {
        $balances = array();

        foreach ($this->_currencies as $currency) {
            if ($this->_accounts[$currency]['sync'] === false) {
                $balances = $this->_getBalance();
                break;
            }
        }

        foreach ($this->_currencies as $currency) {
            if ($this->_accounts[$currency]['sync'] === false) {
              $this->_accounts[$currency]['balance'] = $balances[$currency];
              $this->_accounts[$currency]['sync'] = true;
            }
        }
    }
    
    /**
     *  add to credits
     */
    public function add($amount, $currency) {
        if (!$this->_checkAmount($amount) ) {
            return false;
        }
        if (!$this->_checkAndSetCurrency($currency) ) {
            return false;
        }

        $this->_accounts[$this->_currency]['credits'] += $amount;
        return true;        
    }

    
    /**
     *  add to debits
     */
    public function sub($amount, $currency){
        if (!$this->_checkAmount($amount) ) {
            return false;
        }
        if (!$this->_checkAndSetCurrency($currency) ) {         
            return false;
        }
        
        $this->_accounts[$this->_currency]['debits'] += $amount;
        return true;
    }

    
    protected function _getBalance() {
      return $this->_session->read();
    }

    protected function _updateBalance() {
      $amounts = array();
      foreach($this->_currencies as $currency) {
        $amounts[$currency] = $this->getTransactionBalance($currency);
      }
      if ($this->_session->update($amounts)) {
        foreach($this->_currencies as $currency) {
          $this->_accounts[$currency]['balance'] += $amounts[$currency];
        }
      }
    }
     
    /**
     *  removes/adds amount from funds
     */
    public abstract function commit();
    
    /**
     *  refund
     */    
    public abstract function rollback();

}
