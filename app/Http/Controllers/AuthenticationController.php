<?php

namespace App\Http\Controllers;

use App\Http\Resources\AvatarResource;
use App\JwtHelper;
use App\Http\Resources\UpUsersResource;
use App\Mail\ConfirmRegistrationMail;
use App\Models\Regions;
use App\Models\UpRoles;
use App\Models\UpUsers;
use App\Models\UserAvatars;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthenticationController extends Controller
{
    public function authenticate (Request $request)
    {
        $credentials = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $credentials = ['username' => $request->get('identifier'), 'password'=>$request->get('password')];

        if(Auth::guard('localAuth')->attempt($credentials)){
            $user = Auth::guard('localAuth')->user();
            if(!$user->confirmed){
                return response()->json(Controller::getApiErrorMessage("Your account email is not confirmed"), 400);
            }
            $token = JwtHelper::generateToken($user);
            return response()->json(['jwt' => $token, 'user' => new UpUsersResource($user)]);
        }
        return response()->json(Controller::getApiErrorMessage("Invalid credentials"), 401);
    }


    public function register() {
        $user = Auth::guard('localAuth')->user();
        $data = request()->post();

        if(!$data){
            return response()->json(Controller::getApiErrorMessage("Missing data"),400);
        }

        if(isset($data['clientID'])){
            $user = UpUsers::where('uuid', '=', $data['clientID'])->first();
        }

        $userNameExists = UpUsers::where('username', '=', $data['username'])->first() != null;
        $userEmailExists = UpUsers::where('email', '=', $data['email'])->first() != null;

        if($userNameExists){
            return response()->json(Controller::getApiErrorMessage("Username already taken."), 400);
        }
        if($userEmailExists){
            return response()->json(Controller::getApiErrorMessage("Email already taken."), 400);
        }
        $confirmation_token = Str::random(20);
        $confirmation_token = hash('sha1', $confirmation_token);

        if(!$user){
            $user = UpUsers::create([
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'password' => $data['password'],
                'username' => $data['username'],
                'email' => $data['email'],
                'accepts_surveys' => $data['acceptsSurveys'],
                'is_supporter' => false,
                'is_anonymous' => false,
                'confirmed' => false,
                'blocked' => false,
                'provider' => 'local',
                'referral_count' => 0,
                'referral_code' => Str::uuid(),
                'uuid' => Str::uuid(),
                'confirmation_token' => $confirmation_token,
            ]);
        }
        if($user){
            $user->update([
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'password' => $data['password'],
                'username' => $data['username'],
                'email' => $data['email'],
                'accepts_surveys' => $data['acceptsSurveys'],
                'is_supporter' => false,
                'is_anonymous' => false,
                'confirmed' => false,
                'blocked' => false,
                'provider' => 'local',
                'referral_count' => 0,
                'referral_code' => Str::uuid(),
                'confirmation_token' => $confirmation_token,
            ]);
        }
        // set region of user
        $region = Regions::where('uuid', '=', $data['region']['uuid'])->first();
        if(!$region){
            return response()->json(Controller::getApiErrorMessage('Given Region not found.'), 400);
        }
        $user->avatar()->associate(UserAvatars::first());
        $user->region()->associate($region);
        $region->load('users');
        $region->load('users');
        $user->load('joinedChallenges');

        // set role of user
        $user->role()->associate(UpRoles::find(1));
        $user->save();

        Mail::to($user->email)->send(new ConfirmRegistrationMail(env('APP_URL').'/api/email-confirmation?confirmation='.$confirmation_token));

        // TODO Avatar
        return response()->json([
            'user' => [
                'createdAt' => $user->created_at,
                'id' => $user->id,
                'isSupporter' => $user->is_supporter,
                'provider' => $user->provider,
                'username' => $user->username,
                'uuid' => $user->uuid,
            ]
        ]);
    }
}
