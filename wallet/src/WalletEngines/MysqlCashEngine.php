<?php

namespace RawIron\Wallet\WalletEngines;


class MysqlCashEngine implements Engine {

  private $_log = null;
  private $_dbconn = null;

  public function __construct($connection, $logger) {
    $this->_dbconn = $connection;
    $this->_log = $logger;
  }

  public function read($userId) {
    throw new Exception('Not implemented');
  }


  public function update($userId, $amounts) {

        $query =
        "UPDATE Wallet SET
            Premium= Premium+ FLOOR({$amounts['premium']}),
            Coins= Coins+ FLOOR({$amounts['coins']})          
        WHERE User= {$userId}
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

}
