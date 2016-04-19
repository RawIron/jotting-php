<?php

namespace RawIron\Wallet\WalletEngines;


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
