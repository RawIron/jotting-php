<?php

namespace RawIron\Wallet\Example;

include __DIR__ . "/Bootstrap.php";

use Aura\Di\ContainerBuilder;
use RawIron\Wallet;


$builder = new ContainerBuilder();
$di = $builder->newInstance();

$di->set('logger', $di->lazyNew('RawIron\Wallet\Logger'));

$currencies = Wallet\Currency::getCurrencies();
$di->params['RawIron\Wallet\Engines\MemoryEngine']['currencies'] = $currencies;
$di->set('engine', $di->lazyNew('RawIron\Wallet\Engines\MemoryEngine'));

$userId  = 123;
$di->params['RawIron\Wallet\Engines\WalletStore']['userId'] = $userId;
$di->params['RawIron\Wallet\Engines\WalletStore']['engine'] = $di->lazyGet('engine');
$di->set('walletStore', $di->lazyNew('RawIron\Wallet\Engines\WalletStore'));

$di->params['RawIron\Wallet\CreditTransaction']['session'] = $di->lazyGet('walletStore');
$di->params['RawIron\Wallet\CreditTransaction']['currencies'] = $currencies;
$di->params['RawIron\Wallet\CreditTransaction']['log'] = $di->lazyGet('logger');
$di->set('wallet', $di->lazyNew('RawIron\Wallet\CreditTransaction'));


$wallet  = $di->get('wallet');

$wallet->add(200, 'coins');
$wallet->sub(100, 'coins');

print $wallet->getTransactionBalance('coins');

