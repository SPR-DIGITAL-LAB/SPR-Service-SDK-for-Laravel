<?php
namespace Spr\SprLaravelServiceSdk\RemotePackage;

use Illuminate\Contracts\Auth\Authenticatable;

class AuthToken
{

    
    protected $token;
    protected $authenticatable;

    public function __construct($token, Authenticatable $authenticatable)
    {
        $this->token = $token;
        $this->authenticatable = $authenticatable;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getAuthenticatable()
    {
        return $this->authenticatable;
    }
}
