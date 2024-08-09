<?php

namespace App\Http\Controllers;

use App\Http\Resources\AvatarResource;
use App\Http\Resources\FollowersCollection;
use App\Http\Resources\UpUsersResource;
use App\Models\Regions;
use App\Models\UpUsers;
use App\Models\UserAvatars;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Str;

class UsersController extends Controller
{
    /**
     * function for api route /users/{uuid} which edits data of a user
     * @return array[]|JsonResponse
     */
    public function edit(Request $request, string $uuid)
    {
        $user = UpUsers::where('uuid', '=', $uuid)->where('is_anonymous', '=', false)->first();
        if (!$user) {
            return response()->json(Controller::getApiErrorMessage('Authentication failed'), 400);
        }
        $username = $request->input('username');
        $email = $request->input('email');
        $region = $request->input('region');
        $avatar = $request->input('avatar');

        $password = $request->input('password');
        $passwordRepeat = $request->input('passwordRepeat');

        if ($username) {
            $user->update(['username' => $username]);
        }
        if ($email) {
            $user->update(['email' => $email]);
        }
        if ($password) {
            if ($password == $passwordRepeat) {
                $user->update(['password' => $password]);
            }
            return response()->json(Controller::getApiErrorMessage('Password and repeatedPassword do not match.'), 400);
        }
        if ($region) {
            $region = Regions::where('uuid', '=', $region['uuid'])->first();
            $user->region()->disassociate();
            $user->region()->associate($region);
        }
        if ($avatar) {
            $avatar = UserAvatars::where('uuid', '=', $avatar['uuid'])->first();
            $user->avatar()->disassociate();
            $user->avatar()->associate($avatar);
        }
        $user->save();
        return ['data' => ['user' => (new UpUsersResource($user))]];
    }

    /**
     * function for api route /users/me/follows gets the follows of a logged in user
     * @return array[]|JsonResponse
     */
    function follows()
    {
        $user = Auth::guard('localAuth')->user();
        if (!$user) {
            return response()->json(Controller::getApiErrorMessage('Authentication failed'), 400);
        }
        $following = $user->follows()->get();
        $followers = $user->followers()->get();

        return ['data' => [
            'followers' => (new FollowersCollection($followers)),
            'following' => (new FollowersCollection($following))
        ]
        ];
    }

    /**
     * function for api route POST /users/me/follows to add a friend
     *
     */
    function follow(Request $request)
    {
        $user = Auth::guard('localAuth')->user();
        if (!$user) {
            return response()->json(Controller::getApiErrorMessage('Authentication failed'), 400);
        }
        $ref = $request->ref;
        assert($user instanceof UpUsers);
        $friend = UpUsers::where('referral_code', '=', $ref)->first();

        if (!$friend) {
            return response()->json(Controller::getApiErrorMessage('User not found'), 404);
        }
        assert($friend instanceof UpUsers);
        if ($friend->uuid == $user->uuid) {
            return response()->json(Controller::getApiErrorMessage('Cannot follow yourself'), 409);
        }
        if (in_array($friend->uuid, $user->follows()->get()->pluck('uuid')->toArray())) {
            return response()->json(Controller::getApiErrorMessage('User already followed'), 409);
        }

        $user->follows()->attach($friend);
        $user->followers()->attach($friend);
        TrophiesController::checkReferralCountTrophies($friend);
        TrophiesController::checkReferralCountTrophies($user);

        return ['data' => [
            'uuid' => Str::uuid(),
            'isRequest' => false,
            'user' => [
                'uuid' => $friend->uuid ?? Str::uuid(),
                'username' => $friend->username ?? '',
                'avatar' => new AvatarResource($friend->avatar()->first()),
            ],
        ]
        ];
    }

    /**
     * function for api route /users/me which returns user when logged in
     * @return array[]|JsonResponse
     */
    function getLoggedInUser(Request $request)
    {
        $user = Auth::guard('localAuth')->user();
        if (!$user) {
            return response()->json(Controller::getApiErrorMessage('Authentication failed'), 400);
        }
        return ['data' => ['user' => (new UpUsersResource($user))]];
    }

    function removeFollow(string $uuid)
    {
        $uuid = strtolower($uuid);
        $user = Auth::guard('localAuth')->user();
        if (!$user) {
            return response()->json(Controller::getApiErrorMessage('Authentication failed'), 400);
        }
        assert ($user instanceof UpUsers);
        $userToRemove = UpUsers::where('uuid', '=', $uuid)->first();
        if (!$userToRemove) {
            return response()->json(Controller::getApiErrorMessage('User not found'), 404);
        }

        if (in_array($uuid, $user->follows()->get()->pluck('uuid')->toArray())) {
            $user->follows()->detach($userToRemove->id);
        }
        if (in_array($uuid, $user->followers()->get()->pluck('uuid')->toArray())) {
            $user->followers()->detach($userToRemove->id);
        }
        $user->save();
        $following = $user->follows()->get();
        $followers = $user->followers()->get();
        return ['data' => [
            'followers' => (new FollowersCollection($followers)),
            'following' => (new FollowersCollection($following))
        ]
        ];

    }
}
