<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use App\JwtHelper;
use App\Models\UpUsers;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $this->getToken($request);
        if (isset($token)) {
            // authenticate local users / anonymous users who are not registered
            if ($token == Config::get('auth.iosLocalAccessToken')) {

                // get request header with uuid
                $uuid = request()->header('cleema-install-id');

                // continue without sign in when no install-id header is set
                if (!$uuid) {
                    return $next($request);
                }

                // get user if already exists
                // not only if no anonymous user exists because the cleema-install-id changes each time you log out of a account
                $user = UpUsers::where('uuid', '=', $uuid)->first();

                // check if user exists, if yes then authenticate, if not then create new anonymous user
                if (!$user) {
                    $user = UpUsers::create(['uuid' => $uuid, 'is_anonymous' => true]);
                }
                Auth::guard('localAuth')->loginUsingId($user->id);

                // continue with request
                return $next($request);
            }
            // attempt login guard if jwt token is used
            if (Auth::guard('api')->attempt()) {
                if (Auth::guard('localAuth')->user()) {
                    return $next($request);
                }
                return response()->json(Controller::getApiErrorMessage("Login failed"), 401);
            }
        }

        // if no token is provided --> return error
        return response()->json(Controller::getApiErrorMessage("No bearer token provided"), 401);
    }


    /**
     * using this instead of request()->bearer() because app sends Authentication: "bearer" not "Bearer"
     * @param Request $request
     * @return bool|string|null
     */
    public function getToken(Request $request): bool|string|null
    {
        $header = $request->header('Authorization', '');
        $position = strrpos($header, 'bearer ');
        if ($position !== false) {
            $header = substr($header, $position + 7);
            $token = str_contains($header, ',') ? strstr($header, ',', true) : $header;
            return $token;
        }
        return null;
    }
}
