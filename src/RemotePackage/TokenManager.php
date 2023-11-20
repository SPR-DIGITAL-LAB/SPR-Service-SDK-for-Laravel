<?php

namespace Spr\SprLaravelServiceSdk\RemotePackage;

use Illuminate\Contracts\Auth\Authenticatable;


class TokenManager extends RemoteApi
{
    protected $tokenStore = [];

    public function storeToken(AuthToken $token)
    {
        
        $this->tokenStore[] = $token;
    }

    public function authenticateRequest()
    {
        $token = request()->post('token');

        if ($this->isValidToken($token)) {
            return true;
        }
        return false;
    }

    protected function isValidToken($token)
    {

        return in_array($token, $this->tokenStore);
    }

    public function issueToken(Authenticatable $authenticatable)
    {
       
        $tokenValue = md5($authenticatable->id . uniqid());
        
        $token = new AuthToken($tokenValue, $authenticatable);

        $this->storeToken($token);

        return $token;
    }
}