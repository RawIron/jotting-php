<?php


interface WalletEngine {
  public function read($userId);
  public function update($userId, $balances);
}


class MemoryEngine implements WalletEngine {

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
  }

}


class MysqlEngine implements WalletEngine {

  private $_log = null;
  private $_dbconn = null;

  public function __construct($connection, $logger) {
    $this->_dbconn = $connection;
    $this->_log = $logger;
  }


  public function read($userId) {

      $query = "SELECT Coins, Premium
                FROM Wallet 
                WHERE User= {$userId}
                ";

      $result = mysql_query($query, $this->_dbconn);

      $exceptionMessage = mysql_error($this->_dbconn);
      if ($exceptionMessage) {
        throw new SystemException($exceptionMessage, SystemException::QUERY_FAILURE, 'getWalletBalance', false);
      }

      if (mysql_num_rows($result) != 1) {
        $message = "Could not find Users wallet";
        throw new WalletException($message, 'getWalletBalance', WalletException::NOT_FOUND, false);
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
  

  public function update($userId, $amounts) {
      $query =
      "UPDATE Wallet SET
          Premium= Premium+ FLOOR({$amounts['premium']}),
          Coins= Coins+ FLOOR({$amounts['coins']})                
       WHERE User= {$userId}
      ";
      
      $result=mysql_query($query, $this->_dbconn);
      
      $exceptionMessage = mysql_error($this->_dbconn);
      if ($exceptionMessage) {
        throw new SystemException($exceptionMessage, SystemException::QUERY_FAILURE, 'updateWallet', false);
      }
      
      if (! mysql_affected_rows($this->_dbconn)) {
        $message = "Players wallet not updated.";
        throw new SystemException($message, SystemException::QUERY_FAILURE, 'updateWallet', false);
      }
  
      
      // log update of wallet
      $this->_log->update( array('walletUpdate' => true) );
      $this->_log->append( array('walletPremium' => (int) $amounts['premium']) );
      $this->_log->append( array('walletCoins' => (int) $amounts['coins']) );
      
      return true;
  }

} 
