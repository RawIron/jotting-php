<?php


function getValidSessionToken()
{
    $rid = rand(1, 100);
    $sessionToken = 'user' . $rid;
    return $sessionToken;
}
