<?php

namespace RawIron\Wallet;


interface Engine {
  public function read($userId);
  public function update($userId, $balances);
}


class WalletStore {
    private $_userId = null;
    private $_engine = null;
    private $_messages = array();
    
    public function __construct($userId, $engine) {
        $this->_userId = $userId;
        $this->_engine = $engine;
    }
    
    public function getEngine() {
        return $this->_engine;
    }

    public function read() {
      return $this->_engine->read($this->_userId);
    }

    public function update($amounts) {
      return $this->_engine->update($this->_userId, $amounts);
    }
    
    public function addMessage($request) {
        $this->_messages[] = $request;
    }
    
    public function sentMessages() {
        foreach ($this->_messages as $message) {
            print_r($message);
        }
    }
}


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



class MysqlEngine implements Engine {

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
