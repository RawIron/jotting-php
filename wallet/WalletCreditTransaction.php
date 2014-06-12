<?php

require_once 'WalletTransaction.php';


/**
 * multiple calls of withdraw() are supported
 * only one currency
 * ensures only reserved amount (or less) by withdraw() calls is commited()
 *
 */


class WalletCreditTransaction extends WalletTransaction
{
    public function __construct($session, $log) {
        parent::__construct($session, $log);

        $this->_reset();

        
        $this->_engineMapper['mysql'] = array('_getBalance' => '_getBalance',
                                              '_updateBalance' => '_updateBalance'); 
        if (! isset($this->_engineMapper[$session->getEngine()]) ) {
            return false;
        }
        $this->_engine = $session->getEngine();
    }
    
    public function __destruct() {        
        $this->_engine = false;        
    }
    
    /**
     * clean up transaction data
     */
    protected function _reset() {
        parent::_reset();
        
        foreach ($this->_currencies as $currency) {
            $this->_accounts[$currency]['approved'] = false;
        }
    }

    
    /**
     * read the wallet's balance
     */
    protected function _getBalance()
    {
        $query = "SELECT Coins, Premium
                  FROM Wallet 
                  WHERE User= {$this->_userId}
                  ";

        $result = mysql_query($query, $this->_dbconn);

        $exceptionMessage = mysql_error($this->_dbconn);
        if ($exceptionMessage) {
            throw new SystemException($exceptionMessage, SystemException::QUERY_FAILURE, 'getWalletBalance', false);
        }

        if (mysql_num_rows($result) != 1) {
            $message = "Could not find Users wallet";
            throw new GameException($message, 'getWalletBalance', GameException::DB_UNEXPECTED_RESULT, false);
        }
        
        $row = mysql_fetch_assoc($result);
        
        
        // log wallet state
        $this->_log->append( array( 'walletCheck' => true) );
        $this->_log->append( array( 'walletBalancePremium' => (int) $row['Premium'], 
                                    'walletBalanceCoins' => (int) $row['Coins']) ); 

        $balances['premium'] = (int) $row['Premium'];
        $balances['coins'] = (int) $row['Coins'];
        return $balances;
    }
    
    /**
     * save the wallet's new balance
     */
    protected function _updateBalance($amounts)
    {
        $query =
        "UPDATE Wallet SET
            Premium= Premium+ FLOOR({$amounts['premium']}),
            Coins= Coins+ FLOOR({$amounts['coins']})                
         WHERE User= {$this->_userId}
        ";
        
        $result=mysql_query($query, $this->_dbconn);
        
        $exceptionMessage = mysql_error($this->_dbconn);
        if ($exceptionMessage) {
            throw new SystemException($exceptionMessage, SystemException::QUERY_FAILURE, 'updateWallet', false);
        }
        
        if (! mysql_affected_rows($this->_dbconn)) {
            $message = "Farmers wallet not updated.";
            throw new SystemException($message, SystemException::QUERY_FAILURE, 'updateWallet', false);
        }
    
        
        // log update of wallet
        $this->_log->update( array('walletUpdate' => true) );
        $this->_log->append( array('walletPremium' => (int) $amounts['premium']) );
        $this->_log->append( array('walletCoins' => (int) $amounts['coins']) );
        
        return true;
    }
    
    
    /**
     * check for sufficient funds
     */   
    private function _approve() {
        $this->_setBalances();
        
        // sufficient funds?
        foreach ($this->_currencies as $currency) {
            if( ($this->_accounts[$currency]['amountDebits'] <= $this->_accounts[$currency]['balance']) ) {
                $this->_accounts[$currency]['approved'] = true;
            }
        }

        foreach ($this->_currencies as $currency) {
            if( ($this->_accounts[$currency]['approved'] === false) ) {
                return false;
            }
        }
        
        return true;
    }

    /**
     *  add to debits
     */
    public function sub($amount, $currency)
    {
        if (!parent::sub($amount, $currency)) {
            return false;
        }
        
        // sufficient funds?
        if( $this->_approve() ) {
            return true;
        }
        
        return false;
    }    
    

    public function commit()
    {
        if (! $this->_checkIfOneBalanceChanged()) {
            return true;
        }
        
        foreach ($this->_currencies as $currency) {
            if ($this->getTransactionBalance($currency) < 0) {
                if (!$this->_approve()) {
                    return false;
                }
            }
        }

        $this->{$this->_engineMapper[$this->_engine]['_updateBalance']}($this->_accounts);

        $this->_reset();
        return true;
    }
    
    public function rollback() {           
        $this->_reset();        
        return false;
    }
}

