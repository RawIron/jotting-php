<?php


$x = "hallo.world";
$ax = array('hallo', 'world', array('house', 'dark'));

$x = implode('.', $ax);

print_r($ax);
print_r($x);



$currencies = array('gold', 'silver');

if (in_array('gold', $currencies)) {
    print_r("In: gold \n");
}

foreach ($currencies as $currency) {
    $narray[$currency]['credits'] = 0;
    $narray[$currency]['debits'] = 0;    
    $narray[$currency]['balance'] = false;
}

print_r($narray);
print_r($narray['gold']);


function _getSelf() {
    return 'self';
}

$_engineMapper = array(
    		'mysql' => array('_getBalance' => '_getBalance', '_updateBalance' => '_updateBalance'),
            'self'  => array('_getBalance' => '_getSelf',    '_updateBalance' => '_updateSelf')
            );            
$_engine = 'self';

$response = $_engineMapper[$_engine]['_getBalance']();
print_r($response);



foreach ($currencies as $currency) {
    if ($narray[$currency]['balance'] === false) {
        $balances = array('silver' => 20, 'gold' => 10);
        foreach ($balances as $key => $value) {
            $narray[$key]['balance'] = $value;
        }
        break;
    }
} 
print_r($narray);
