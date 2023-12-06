<?php

namespace Spr\SprLaravelServiceSdk\RemotePackage;

 abstract class APIKeyManager
{

    public function authenticate()
    {
        $token = request()->bearerToken();
        return $this->resolve($token);
    }

    abstract public function resolve($token);
    
}
