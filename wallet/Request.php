<?php
 
require_once 'WalletCreditTransaction.php';
require_once 'WalletEngines.php';


class DsSession {
    private $_userId = null;
    private $_connection = false;
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


class Logger {
    public function append($message) {
        return true;
    }
}



class WalletCreditTransactionTest extends PHPUnit_Framework_TestCase {

  private function wallet() {
    $currencies = Currencies::getCurrencies();
    $engine = new MemoryEngine($currencies);
    $userId  = 123;
    $session = new DsSession($userId, $engine);
    $logger  = new Logger();
    $wallet  = new WalletCreditTransaction($session, $currencies, $logger);
    return $wallet;
  }


  public function test_two_currency_balance() {
    $wallet = $this->wallet();
    $wallet->add(24,'premium');
    $wallet->add(24,'premium');
    $wallet->add(400,'coins');
    $wallet->sub(120,'coins');

    $balance = $wallet->getTransactionBalance('premium');
    $this->assertEquals(48, $balance);
    $balance = $wallet->getTransactionBalance('coins');
    $this->assertEquals(280, $balance);

    $balance = $wallet->getAccountBalance('premium');
    $this->assertEquals(0, $balance);
    $balance = $wallet->getAccountBalance('coins');
    $this->assertEquals(0, $balance);

    $wallet->commit();

    $balance = $wallet->getTransactionBalance('premium');
    $this->assertEquals(0, $balance);
    $balance = $wallet->getTransactionBalance('coins');
    $this->assertEquals(0, $balance); 

    $balance = $wallet->getAccountBalance('premium');
    $this->assertEquals(48, $balance);
    $balance = $wallet->getAccountBalance('coins');
    $this->assertEquals(280, $balance);
  }

}
