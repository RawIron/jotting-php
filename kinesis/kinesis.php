<?php

require './aws.phar';
require './conf.php';

use Aws\Kinesis\KinesisClient;


class AwsKinesis
{
    public static function createClient($keys)
    {
        $client = KinesisClient::factory(array(
            'key' => $keys['key'],
            'secret' => $keys['secret'],
            'token' => $keys['token'],
            'region' => $keys['region'],
        ));

        if (AWS_KINESIS_DEBUG || AWS_DEBUG) {
            $logPlugin = Guzzle\Plugin\Log\LogPlugin::getDebugPlugin();
            $client->addSubscriber($logPlugin);
        }

        return $client;
    }

    public static function factory($keys)
    {
        if (!$keys) {
            $keys = array(
                'key' => AWS_KINESIS_ACCESS_KEY_ID,
                'secret' => AWS_KINESIS_SECRET_ACCESS_KEY,
                'region' => AWS_KINESIS_REGION,
                'token' => null,
            );
        }
        $stream = AWS_KINESIS_STREAM;
        $client = AwsKinesis::createClient($keys);
        return new AwsKinesis($client, $stream);
    }

    private $client = null;
    private $stream = null;

    public function __construct($client, $stream)
    {
        $this->client = $client;
        $this->stream = $stream;
    }

    public function describe()
    {
        return $this->client->describeStream(array(
            'StreamName' => $this->stream,
        ));
    }

    public function getShards()
    {
        $response = $this->describe();
        return $response['StreamDescription']['Shards'];
    }

    public function getARN()
    {
        $response = $this->describe();
        return $response['StreamDescription']['StreamARN'];
    }

    public function isActive()
    {
        $response = $this->describe();
        if ($response['StreamDescription']['StreamStatus'] == 'ACTIVE') {
            return true;
        } else {
            return false;
        }
    }
}


class AwsKinesisProducer
{
    public static function factory($keys)
    {
        if (! $keys) {
          $keys = array(
              'key' => AWS_KINESIS_ACCESS_KEY_ID,
              'secret' => AWS_KINESIS_SECRET_ACCESS_KEY,
              'region' => AWS_KINESIS_REGION,
              'token' => null,
          );
        }
        $client = AwsKinesis::createClient($keys);
        $stream = AWS_KINESIS_STREAM;

        return new AwsKinesisProducer($client, $stream);
    }

    private $client = null;
    private $stream = null;

    public function __construct($client, $stream)
    {
        $this->client = $client;
        $this->stream = $stream;
    }

    public function write($event)
    {
        return $this->client->putRecord(array(
            'StreamName' => $this->stream,
            'Data' => json_encode($event),
            'PartitionKey' => $event['user_id'],
        ));
    }

}


class AwsKinesisConsumer
{
    public static function factory()
    {
        $keys = array(
            'key' => AWS_KINESIS_ACCESS_KEY_ID,
            'secret' => AWS_KINESIS_SECRET_ACCESS_KEY,
            'region' => AWS_KINESIS_REGION,
            'token' => null,
        );
        $client = AwsKinesis::createClient($keys);
        $kinesis = AwsKinesis::factory($keys);
        $stream = AWS_KINESIS_STREAM;

        return new AwsKinesisConsumer($client, $stream, $kinesis);
    }

    private $client = null;
    private $stream = null;
    private $kinesis = null;

    public function __construct($client, $stream, $kinesis)
    {
        $this->client = $client;
        $this->stream = $stream;
        $this->kinesis = $kinesis;
    }

    public function read($limit=100)
    {
        $shards = $this->kinesis->getShards();

        $shardId = $shards[0]['ShardId'];
        $response = $this->client->getShardIterator(array(
            'StreamName' => $this->stream,
            'ShardId' => $shardId,
            'ShardIteratorType' => 'TRIM_HORIZON',
        ));

        $iterator = $response['ShardIterator'];

        $response = $this->client->getRecords(array(
            'ShardIterator' => $iterator,
            'Limit' => $limit,
        ));

        return $response;
    }
}
