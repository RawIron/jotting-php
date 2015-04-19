<?php

require_once(dirname(__FILE__) . '/config.php');
require_once('EventWorker.php');
require_once('LoggerFactory.php');
require_once('MessageQueueClientFactory.php');
require_once('DataStoreFactory.php');

$config = new Config();
$config->setup();

DataStoreFactory::setup($config);
$logger = LoggerFactory::create('Test');
$logger->logTo('gearman', 'event');
$config->setLogger($logger);
$client = MessageQueueClientFactory::create('Gearman');
$client->setup($config);

$worker = new EventWorker($config);
$worker->useMessage($client);
$worker->useLog($logger);
$worker->work();
