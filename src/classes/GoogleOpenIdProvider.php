<?php


namespace rizwanjiwan\common\classes;


use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Token\AccessToken;

class GoogleOpenIdProvider extends Google
{

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        //override parent to use the correct endpint now that google plus has shutdown
        //https://accounts.google.com/.well-known/openid-configuration is different but we'll use what parent uses for now
        return 'https://www.googleapis.com/oauth2/v3/userinfo';
    }
}