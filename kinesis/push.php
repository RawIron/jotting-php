<?php

require './kinesis.php';


$eventData = array(
  'type' => 'ux',
  'name' => 'sentMessage',
  'user_id' => '232453q345',
);

$aws_access_keys = [];

$kinesisProducer = AwsKinesisProducer::factory($aws_access_keys);
$result = $kinesisProducer->write($eventData);
print_r($result);

