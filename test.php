<?php

include 'vendor/autoload.php';
$credentialsFilePath = 'firebase.json';
$client = new Google_Client();
$client->setAuthConfig($credentialsFilePath);

$client->addScope([
    'https://www.googleapis.com/auth/userinfo.email',
    'https://www.googleapis.com/auth/firebase.database',
    'https://www.googleapis.com/auth/firebase.messaging',
]);
$client->setApplicationName('GoogleAnalytics');
$client->refreshTokenWithAssertion();
$token = $client->getAccessToken();
$accessToken = $token['access_token'];
print($accessToken);
