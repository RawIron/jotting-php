<?php

namespace RawIron\Wallet;


class Logger {
    private $_counter = 0;

    public function totalSent() {
      return $this->_counter;
    }

    public function append($message) {
        ++$this->_counter;
        return true;
    }
}
