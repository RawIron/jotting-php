<?php

namespace RawIron\Wallet;

require_once 'WalletTransaction.php';

/**
 * multiple calls of sub() and add() are supported
 * only one currency
 *
 */

class WalletCashTransaction extends WalletTransaction {    

    public function __construct($session, $currencies, $log) {
        parent::__construct($session, $currencies, $log);
        $this->_reset();    
    }
    
    protected function _reset() {
        parent::_reset();

        foreach ($this->_currencies as $currency) {
            $this->_accounts[$currency]['withdrawn'] = 0;
        }
    }


    /**
     *  test for sufficient funds
     */
    public function sub($amount, $currency) {
        if (!parent::sub($amount, $currency)) {
            return false;
        }
        
        if ($this->_accounts[$this->_currency]['debits'] == 0) {
            return true;
        }
        
        foreach ($this->_currencies as $currency) {
            $amounts[$currency] = (-1) * $this->_accounts[$currency]['debits'];
        }
                
        if ( !$this->_updateBalance($amounts) ) {
            return false;
        }

        foreach ($this->_currencies as $currency) {
            $this->_accounts[$this->_currency]['withdrawn'] = $this->_accounts[$currency]['debits'];           
            $this->_accounts[$currency]['debits'] = 0;
        }
        
        return true;
    }
    

    public function commit() {
        if (! $this->_checkIfOneBalanceChanged()) {
            return true;
        }
        
        foreach ($this->_currencies as $currency) {
            if ( $this->_accounts[$currency]['debits'] > 0 ) {
                return false;
            }
            $amounts[$currency] = $this->_accounts[$currency]['credits'];            
        }

        if ( !$this->_updateBalance($amounts) ) {
            return false;
        }        

        $this->_reset();
        return true;
    }
    

    public function rollback() {
        $total = 0;
        foreach ($this->_currencies as $currency) {
            $amounts[$currency] = $this->_accounts[$this->_currency]['withdrawn'];
            $total += $amounts[$currency];
        }
        
        if ($total === 0) {
            return true;
        }
        
        if ( !$this->_updateBalance($amounts) ) {
            return false;
        }

        $this->_log->append(array('walletRollback' => true));
                        
        $this->_reset();
        return true;
    }
}
