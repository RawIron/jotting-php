<?php
 
require_once 'WalletCreditTransaction.php';


class DsSession {
    private $_userId = null;
    private $_connection = false;
    private $_engine   = 'mysql';
    private $_messages = array();
    
    public function __construct($userId, $engine) {
        $this->_userId = $userId;
        $this->_engine = $engine;
    }
    
    public function getEngine() {
        return $this->_engine;
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


$userId  = 123;
$logger  = new Logger();

$session = new DsSession($userId, 'self');
$wallet  = new WalletCreditTransaction($session, $logger);

$wallet->add(24,'premium');
$wallet->add(24,'premium');
$wallet->add(400,'coins');
$wallet->sub(120,'coins');
$balance = $wallet->getTransactionBalance('premium');
print_r($balance);

$wallet->commit();

$balance = $wallet->getAccountBalance('premium');
print_r("premium:" . $balance);
$balance = $wallet->getAccountBalance('coins');
print_r("coins:" . $balance);

