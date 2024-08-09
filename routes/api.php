<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ChallengesController;
use App\Http\Controllers\InfosController;
use App\Http\Controllers\NewsEntriesController;
use App\Http\Controllers\OffersController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\QuizzesController;
use App\Http\Controllers\SurveysController;
use App\Http\Controllers\TrophiesController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\ApiAuth;
use App\Http\Resources\AvatarCollection;
use App\Http\Resources\ChallengeTemplatesCollection;
use App\Http\Resources\NewsTagsCollection;
use App\Http\Resources\RegionsCollection;
use App\Models\ChallengeTemplates;
use App\Models\NewsTags;
use App\Models\Regions;
use App\Models\UpUsers;
use App\Models\UserAvatars;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(ApiAuth::class)->controller(UsersController::class)->prefix('/users/me')->group(function (){
    Route::get('/', 'getLoggedInUser');
    Route::get('/follows', 'follows');
    Route::post('/follows', 'follow');
    Route::delete('/follows/{uuid}', 'removeFollow');
});

Route::middleware(ApiAuth::class)->get('/user-avatars', function () {
    return new AvatarCollection(UserAvatars::all());
});

Route::middleware(ApiAuth::class)->patch('/users/{uuid}', [UsersController::class, 'edit']);

Route::prefix('projects')->controller(ProjectsController::class)->middleware(ApiAuth::class)->group(function(){
    Route::get('/', 'fetch');
    Route::get('/{uuid}/', 'fetchOne');
    Route::patch('/{uuid}/fav', 'fav');
    Route::patch('/{uuid}/unfav', 'unfav');
    Route::patch('/{uuid}/join', 'join');
    Route::patch('/{uuid}/leave', 'leave');
});

Route::prefix('trophies')->controller(TrophiesController::class)->middleware(ApiAuth::class)->group(function () {
    Route::get('/me', 'getUsersTrophies');
    Route::get('/me/new', 'getUsersNewTrophies');
    Route::get('/{uuid}', 'fetchOne');
});

// news_entry routes
Route::prefix('news-entries')->controller(NewsEntriesController::class)->middleware(ApiAuth::class)->group(function () {
    Route::get('/', 'fetch');
    Route::get('/{uuid}/', 'fetchOne');
    Route::patch('/{uuid}/read', 'readEntry');
    Route::patch('/{uuid}/fav', 'favEntry');
    Route::patch('/{uuid}/unfav', 'unfavEntry');
});

Route::middleware(ApiAuth::class)->get('/news-tags',function () {
    return (new NewsTagsCollection(NewsTags::all()))->toWrappedArray();
    // TODO here is some kind of pagination missing from the original strapi but that seems unnecessary
});

Route::prefix('challenges')->controller(ChallengesController::class)->middleware(ApiAuth::class)->group(function () {
    Route::get('/', 'fetch');
    Route::post('/', 'create');
    Route::patch('/{uuid}/join', 'join');
    Route::patch('/{uuid}/leave', 'leave');
    Route::get('/{uuid}', 'fetchOne');
    Route::patch('/{uuid}/answer', 'answer');
});

Route::middleware(ApiAuth::class)->get('/challenge-templates', function (){
    return new ChallengeTemplatesCollection(ChallengeTemplates::all());
});
Route::POST('/auth/local', [AuthenticationController::class, 'authenticate']);
Route::middleware(ApiAuth::class)->post('/auth/local/register', [AuthenticationController::class, 'register']);

Route::middleware(ApiAuth::class)->get('/quizzes/current',[QuizzesController::class, 'fetch']);
Route::middleware(ApiAuth::class)->post('/quiz-responses', [QuizzesController::class, 'respond']);

Route::middleware(ApiAuth::class)->get('/regions', function () {
    return (new RegionsCollection(
        Regions::where('is_public', '=', 1)
            ->whereNotNull('name')
            ->whereNotNull('uuid')
            ->where(function ($query) {
                $query->where('is_supraregional', '=', false)
                    ->orWhereNull('is_supraregional');
            })
            ->get()
            ->filter()));
});


Route::middleware(ApiAuth::class)->get('/offers', [OffersController::class, 'fetch']);
Route::middleware(ApiAuth::class)->patch('/offers/{uuid}/redeem', [OffersController::class, 'redeem']);

Route::prefix('offers')->controller(OffersController::class)->middleware(ApiAuth::class)->group(function () {
    Route::get('/', 'fetch');
    Route::get('/{uuid}/', 'fetchOne');
    Route::patch('/offers/{uuid}/redeem', 'redeem');
});

Route::prefix('surveys')->controller(SurveysController::class)->middleware(ApiAuth::class)->group(function () {
    Route::get('', 'fetch');
    // Surveys are currently just links, so nothing more to do than fetch
    //Route::post('/respond', 'respond');
    // uuid/participate
});

Route::middleware(ApiAuth::class)->get('/about', [InfosController::class, 'about']);
Route::middleware(ApiAuth::class)->get('/privacy-policy', [InfosController::class, 'privacyPolicy']);
Route::middleware(ApiAuth::class)->get('/legal-notice', [InfosController::class, 'legalNotice']);
Route::middleware(ApiAuth::class)->get('/partnership', [InfosController::class, 'partnership']);

Route::get('/email-confirmation', function () {
    if(!request('confirmation')){
        return 'not allowed';
    }
    $confirmationToken = request('confirmation');
    $user = UpUsers::where('confirmation_token', '=', $confirmationToken)->first();
    if(!$user){
        return 'not allowed';
    }

    $link = env('APP_URL').'/api/auth/email-confirmation?confirmation='.$user->confirmation_token;
    return view('email-confirmation',['link' => $link]);
});

Route::get('auth/email-confirmation', function () {
    if(!request('confirmation')){
        return 'not allowed';
    }
    $confirmationToken = request('confirmation');
    $user = UpUsers::where('confirmation_token', '=', $confirmationToken);
    if(!$user){
        return 'not allowed';
    }
    $user->update(['confirmed' => true, 'confirmation_token' => null]);
    return view('email-confirmation--successful');
});



