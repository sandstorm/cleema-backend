<?php

namespace App;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class JwtHelper
{
    public static function generateToken($user)
    {
        $payload = [
            'iss' => Config::get('app.url'),
            'id' => $user->id,
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addSeconds(Config::get('jwt.ttl'))->timestamp,
        ];

        return JWT::encode($payload, Config::get('jwt.secret'), 'HS256');
    }

    public static function decodeToken($token): \stdClass
    {
        return JWT::decode($token, new Key(Config::get('jwt.secret'),'HS256'));
    }
}
