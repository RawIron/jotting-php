<?php

namespace RawIron\Wallet;


class Currencies {
    private static $_currencies = array('premium', 'coins');
    
    public static function getCurrencies() {
        return self::$_currencies;
    }
}
