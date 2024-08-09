<?php

namespace App\Guards;


use App\JwtHelper;
use Carbon\Carbon;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class JwtGuard implements Guard
{
    use GuardHelpers;

    protected $user = null;

    public function __construct(UserProvider $provider)
    {
        $this->provider = $provider;
    }

    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        if ($this->getToken() && $user = $this->validate(['jwt' => $this->getToken()])) {
            return $this->user = $user;
        }
    }

    public function validate(array $credentials = []): bool | Authenticatable | JsonResponse
    {
        if (empty($credentials['jwt'])) {
            return false;
        }

        try {
            $decoded = JwtHelper::decodeToken($credentials['jwt']);

            if($decoded->exp < Carbon::now()->timestamp){
                return false;
            }

            // You may perform additional validation here
            return $this->user = $this->provider->retrieveById($decoded->id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function attempt()
    {
        $user = $this->user();
        if($user){

            Auth::guard('localAuth')->login($user);
            return true;
        }
        return false;
    }

    /**
     * custom getToken methods because the cleema app send 'bearer' not 'Bearer
     * @param Request $request
     * @return false|string|null
     */
    public function getToken ()
    {
        $header = request()->header('Authorization', '');
        $position = strrpos($header, 'bearer ');
        if ($position !== false) {
            $header = substr($header, $position + 7);
            $token = str_contains($header, ',') ? strstr($header, ',', true) : $header;
            return $token;
        }
        return null;
    }
}

