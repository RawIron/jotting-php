<?php

define("AWS_DEBUG", false);

define("AWS_KINESIS_DEBUG", false);
define("AWS_KINESIS_REGION", "us-east-1");
define("AWS_KINESIS_ACCESS_KEY_ID", "<YOUR_ACCESS_KEY>");
define("AWS_KINESIS_SECRET_ACCESS_KEY", "<YOUR_SECRET_KEY>");

define("AWS_KINESIS_STREAM", "frontend_events");

define("AWS_COGNITO_DEBUG", false);
define("AWS_COGNITO_REGION", "us-east-1");
define("AWS_COGNITO_ACCESS_KEY_ID", "<YOUR_ACCESS_KEY>");
define("AWS_COGNITO_SECRET_ACCESS_KEY", "<YOUR_SECRET_KEY>");
define("AWS_COGNITO_IDENTITY_POOL_ID", "us-east-1:<YOUR_POOL_ID>");
define("AWS_COGNITO_PROVIDER_NAME", "login.example.com");

define("AWS_STS_DEBUG", false);
define("AWS_IAM_ROLE_ARN", "arn:aws:iam::<YOUR_ID>:role/Cognito_Kinesis_FrontendEvents");
