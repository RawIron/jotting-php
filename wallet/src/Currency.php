<?php

namespace RawIron\Wallet;


class Currency {
    private static $_currencies = array('premium', 'coins');
    
    public static function getCurrencies() {
        return self::$_currencies;
    }
}
