<?php

require_once 'WalletTransaction.php';


/**
 * multiple calls of sub() and add() are supported
 * only one currency
 *
 */

class WalletCashTransaction extends WalletTransaction {    

    public function __construct($userId, $connection, LogRecord $log) {
        parent::__construct($userId, $connection, $log);
        $this->_reset();    
    }
    
    /**
     * clean up transaction data
     *
     */
    protected function _reset() {
        parent::_reset();
        
        foreach ($this->_currencies as $currency) {
            $this->_accounts[$currency]['withdrawn'] = 0;
        }
    }

    /**
     * write to data store
     */      
    protected function _updateBalance($amounts) {
        parent::_updateBalance($amounts);        

        $query =
        "UPDATE Wallet SET
            Premium= Premium+ FLOOR({$amounts['premium']}),
            Coins= Coins+ FLOOR({$amounts['coins']})          
        WHERE User= {$this->_userId}
        AND Premium+ {$amounts['premium']} >= 0 
        AND Coins+ {$amounts['coins']} >= 0                      
        ";
        
        $result=mysql_query($query, $this->_dbconn);
        
        $exceptionMessage = mysql_error($this->_dbconn);
        if ($exceptionMessage) {
            throw new SystemException($exceptionMessage, SystemException::QUERY_FAILURE, 'updateWallet', false);
        }
    

        if ( !mysql_affected_rows($this->_dbconn)) {
            $this->_log->append( array('walletNSF' => true) );            
            return false;
        
        } else {
            // log update of wallet
            $this->_log->update( array('walletUpdate' => true) );
            $this->_log->append( array('walletPremium' => (int) $amounts['premium']) );
            $this->_log->append( array('walletCoins' => (int) $amounts['coins']) );

            return true;
        }
    }

    /**
     *  test for sufficient funds
     */
    public function sub($amount, $currency) {
        if (!parent::sub($amount, $currency)) {
            return false;
        }
        
        if ($this->_accounts[$this->_currency]['amountDebits'] == 0) {
            return true;
        }
        
        foreach ($this->_currencies as $currency) {
            $amounts[$currency] = (-1) * $this->_accounts[$currency]['amountDebits'];
        }
                
        if ( !$this->_updateBalance($amounts) ) {
            return false;
        }

        foreach ($this->_currencies as $currency) {
            $this->_accounts[$this->_currency]['withdrawn'] = $this->_accounts[$currency]['amountDebits'];           
            $this->_accounts[$currency]['amountDebits'] = 0;
        }
        
        return true;
    }
    

    public function commit() {
        if (! $this->_checkIfOneBalanceChanged()) {
            return true;
        }
        
        foreach ($this->_currencies as $currency) {
            if ( $this->_accounts[$currency]['amountDebits']>0 ) {
                return false;
            }
            $amounts[$currency] = $this->_accounts[$currency]['amountCredits'];            
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
