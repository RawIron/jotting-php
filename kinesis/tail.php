<?php

require './kinesis.php';

$kinesis = AwsKinesis::factory(array());
print_r($kinesis->describe());

$kinesisConsumer = AwsKinesisConsumer::factory();
$result = $kinesisConsumer->read();
print_r($result);
