<?php

require './aws.phar';
require './conf.php';

use Aws\Sts\StsClient;


class AwsSimpleTokenService
{
    public static function factory()
    {
        $iamRole = AWS_IAM_ROLE_ARN;
        $region = AWS_KINESIS_REGION;
        $client = AwsSimpleTokenService::createClient();
        return new AwsSimpleTokenService($client, $iamRole, $region);
    }

    public static function createClient()
    {
        $client = StsClient::factory(array(
            'region' => AWS_COGNITO_REGION,
        ));

        if (AWS_STS_DEBUG || AWS_DEBUG) {
            $logPlugin = Guzzle\Plugin\Log\LogPlugin::getDebugPlugin();
            $client->addSubscriber($logPlugin);
        }

        return $client;
    }

    private $client = null;
    private $iamRole = null;
    private $region = null;

    public function __construct($client, $iamRole, $region)
    {
        $this->client = $client;
        $this->iamRole = $iamRole;
        $this->region = $region;
    }

    public function exchangeToken($sessionToken, $token)
    {
        $response = $this->client->assumeRoleWithWebIdentity(array(
            'RoleArn' => $this->iamRole,
            'RoleSessionName' => $sessionToken,
            'WebIdentityToken' => $token,
        ));

        $credentials = array(
            'key' => $response['Credentials']['AccessKeyId'],
            'secret' => $response['Credentials']['SecretAccessKey'],
            'token' => $response['Credentials']['SessionToken'],
            'region' => $this->region,
        );

        return $credentials;
    }
}
