<?php

require './aws.phar';
require './conf.php';

use Aws\CognitoIdentity\CognitoIdentityClient;


class AwsCognito
{
    public static function factory()
    {
        $pool = AWS_COGNITO_IDENTITY_POOL_ID;
        $provider = AWS_COGNITO_PROVIDER_NAME;
        $client = AwsCognito::createClient();
        return new AwsCognito($client, $pool, $provider);
    }

    private static function createClient()
    {
        $client = CognitoIdentityClient::factory(array(
            'key' => AWS_COGNITO_ACCESS_KEY_ID,
            'secret' => AWS_COGNITO_SECRET_ACCESS_KEY,
            'region' => AWS_COGNITO_REGION,
        ));

        if (AWS_COGNITO_DEBUG || AWS_DEBUG) {
            $logPlugin = Guzzle\Plugin\Log\LogPlugin::getDebugPlugin();
            $client->addSubscriber($logPlugin);
        }

        return $client;
    }

    const TOKEN_TTL = 300;

    private $client = null;
    private $identityPool = null;
    private $provider = null;

    public function __construct($client, $identityPool, $provider)
    {
        $this->client = $client;
        $this->identityPool = $identityPool;
        $this->provider = $provider;
    }

    public function requestToken($sessionToken)
    {
        $token = $this->client->getOpenIdTokenForDeveloperIdentity(array(
            'IdentityPoolId' => $this->identityPool,
            'Logins' => array(
                $this->provider => $sessionToken,
            ),
            'TokenDuration' => AwsCognito::TOKEN_TTL,
        ));

        return $token;
    }

    public function lookupDeveloperId($sessionId)
    {
        $result = $this->client->lookupDeveloperIdentity(array(
            'IdentityPoolId' => $this->identityPool,
            'DeveloperUserIdentifier' => $sessionId,
            'MaxResults' => 10,
        ));

        return $result;
    }
}
