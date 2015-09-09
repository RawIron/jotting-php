<?php

require './session.php';

$sessionToken = getValidSessionToken();

require './cognito.php';
require './sts.php';
require './kinesis.php';

$cognito = AwsCognito::factory();
$response = $cognito->requestToken($sessionToken);

$cognitoToken = $response['Token'];
$identityId = $response['IdentityId'];

echo PHP_EOL;
print_r($cognitoToken);
echo PHP_EOL;
print_r($identityId);

//
//$response = $cognito->lookupDeveloperId($sessionToken);
//echo PHP_EOL;
//print_r($response['DeveloperUserIdentifierList'][0]);



$sts = AwsSimpleTokenService::factory();
$aws_access_keys = $sts->exchangeToken($sessionToken, $cognitoToken);

echo PHP_EOL;
print_r($aws_access_keys);

//
//$kinesis = AwsKinesis::factory(array());
//$streamInfo = $kinesis->describe();
//print_r($streamInfo);

$eventData = array(
  'type' => 'ux',
  'name' => 'sentMessage',
  'user_id' => $sessionToken,
);

$kinesisProducer = AwsKinesisProducer::factory($aws_access_keys);
$result = $kinesisProducer->write($eventData);
print_r($result);

sleep(1);

//
//$kinesisConsumer = AwsKinesisConsumer::factory();
//$result = $kinesisConsumer->read();
//print_r($result);
