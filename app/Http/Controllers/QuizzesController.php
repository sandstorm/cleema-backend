<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuizzesResource;
use App\Models\QuizQuestions;
use App\Models\QuizStreaks;
use App\Models\Quizzes;

use App\Models\Regions;
use App\Models\Trophies;
use Date;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class QuizzesController extends Controller
{

    public function respond()
    {
        $user = Auth::guard('localAuth')->user();
        if(!$user){
            return response()->json(Controller::getApiErrorMessage('Authentication failed.'), 400);
        }
        $response = request()->input('data');
        if(!$response){
            return response()->json(Controller::getApiErrorMessage('Data missing.'), 400);
        }
        $answer = $response['answer'];
        $quizUuid =$response['quiz'];
        if(!$answer || !$quizUuid){
            return response()->json(Controller::getApiErrorMessage('Data missing.'), 400);
        }

        $quiz = Quizzes::where('uuid', '=', $quizUuid)->first();

        if(empty($quiz)){
            return response()->json(Controller::getApiErrorMessage('Quiz not found.'), 404);
        }

        $quizQuestion = $quiz->quizQuestion()->first();
        $quizRegion = $quizQuestion->region()->first();
        $quizRegionUUID = null;
        if (!empty($quizRegion)) {
            $quizRegionUUID = $quizRegion->uuid;
            $currentQuiz = $this->getCurrent($quizRegionUUID);
        } else {
            $currentQuiz = $this->getCurrent();
        }

        if(!$currentQuiz){
            return response()->json(Controller::getApiErrorMessage('Current quiz not found.'), 404);
        }

        if($currentQuiz->uuid != $quizUuid){
            return response()->json(Controller::getApiErrorMessage('You can only answer the current quiz.'), 400);
        }

        // prevent 2 responses to the same quiz
        if($user->quizResponses()->where('id', '=', $currentQuiz->id)->exists()){
            return response()->json(Controller::getApiErrorMessage('User already responded to that quiz.'), 400);
        }

        $date = Carbon::now();
        DB::insert('INSERT INTO quiz_responses_v2 (answer, user_id, date, quiz_id)
                           VALUES (?, ?, ?, ?)',
                    [$answer, $user->id, $date, $currentQuiz->id]
        );

        $streak = $user->quizStreak;
        $previousQuiz = $this->checkDateForQuiz(Carbon::make($currentQuiz->date)->subDay()->format('Y-m-d'), $quizRegionUUID);
        $previousResponse = $previousQuiz ? $previousQuiz->responses()->where('id', '=', $user->id)->first() : null;

        if(!$streak){
            $streak = QuizStreaks::create([
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'max_correct_answer_streak' => 0,
                'correct_answer_streak' => 0,
                'participation_streak' => 0
            ]);
            $user->quizStreak()->associate($streak);
            $user->save();
        }

        if(!$previousQuiz || $previousResponse){
            $streak->update(['participation_streak' => $streak->participation_streak+1]);
            if($answer == $currentQuiz->correct_answer){
                $streak->update(['correct_answer_streak' => $streak->correct_answer_streak+1]);
            }
        } else{
            $streak->update([
                'participation_streak' => 1,
                'correct_answer_streak' => ($answer == $currentQuiz->correct_answer ? 1 : 0)
            ]);
        }
        if($streak->correct_answer_streak > $streak->max_correct_answer_streak){
            $streak->update(['max_correct_answer_streak' => $streak->correct_answer_streak]);
        }
        $streak->save();
        // check if trophy has to be awarded
        $trophyAwarded = TrophiesController::checkQuizTrophies();

        return [
            'data' => [
                'answer' => $answer,
                'date' => $date,
                'uuid' => $quizUuid,
            ],
            'meta' => [
                'trophyAwarded' => $trophyAwarded,
            ]
        ];
    }

    public function fetch ()
    {
        $regionUUID = request('region');
        $filters = request('filters');
        if(isset($filters['$or']['1']['region']['uuid']['$eq'])) {
            $regionUUID = $filters['$or']['1']['region']['uuid']['$eq'];
        }

        // For older app versions, which do not send a region on quizzes fetch requests, we do this
        if ($regionUUID == null) {
            $user = Auth::guard('localAuth')->user();
            if ($user != null) {
                $userRegion = $user->region()->first();
                if ($userRegion != null) {
                    $regionUUID = $userRegion->uuid;
                }
            }
        }

        $currentQuiz = $this->getCurrent($regionUUID);

        return [
            'data' => $currentQuiz ? new QuizzesResource($currentQuiz) : null
        ];
    }

    public function getCurrent($regionUUID = null)
    {
        $date = Carbon::now()->toDateString();

        $currentQuiz = $this->checkDateForQuiz($date, $regionUUID ?? null);

        // If today has neither regional, nor supraregional Quiz -> create Filler Quiz
        if (!$currentQuiz) {
            $currentQuiz = $this->createFillerQuiz();
        }
        return $currentQuiz;
    }

    public function checkDateForQuiz($date, String $userRegionUuid = null)
    {
        if ($userRegionUuid != null) {
            $userRegionId = Regions::where('uuid', $userRegionUuid)->first()->id;

            $currentQuiz = Quizzes::query()->where('date', '=', $date)
                ->whereRelation('quizQuestion', 'region_id', '=', $userRegionId)
                ->first();
            if ($currentQuiz) {
                return $currentQuiz;
            }
        }

        $supraregionalRegion = Regions::where('is_supraregional', true)->first();
        $supraregionalRegionUuid = null;
        if (!empty($supraregionalRegion)) $supraregionalRegionUuid = $supraregionalRegion->uuid;

        if ($supraregionalRegionUuid != null) {
            $regionId = $supraregionalRegion->id;
            $currentQuiz = Quizzes::where('date', '=', $date)
                ->whereRelation('quizQuestion', 'region_id', '=', $regionId)
                ->first();
            return $currentQuiz;
        }
        return null;
    }

    // Filler Quizzes are supraregional quizzes for when a region has no regional quiz
    public function createFillerQuiz(): Quizzes | null
    {
        $fillerQuizQuestions = QuizQuestions::query()->where('is_filler', '=', true)->get();

        if ($fillerQuizQuestions->isEmpty()) return null;

        $fillerQuizzes = Quizzes::query()
            ->whereRelation('quizQuestion', 'is_filler', '=', true)
            ->get();

        $quizQuestion = null;

        if (!$fillerQuizzes->isEmpty()) {
            $quizQuestion = QuizQuestions::query()
                ->where('is_filler', '=', true)
                ->where('id', '>', $fillerQuizzes
                    ->sortBy('date', descending: true)
                    ->first()->quiz_question_id
                )->first();

            if($quizQuestion == null) {
                $quizQuestion = QuizQuestions::query()
                    ->where('is_filler', '=', true)
                    ->where('id', '=', $fillerQuizzes
                        ->sortBy('date', descending: false)
                        ->first()->quiz_question_id
                    )
                    ->first();
            }
        }

        if ($quizQuestion == null) {
            $quizQuestion = QuizQuestions::query()
                ->where('is_filler', '=', true)
                ->where('id', '=', $fillerQuizQuestions
                    ->first()->id
                )
                ->first();
        }

        $newFillerQuiz = new Quizzes([
            'date' => Carbon::now()->toDateString(),
        ]);
        $newFillerQuiz->quizQuestion()->associate($quizQuestion)->save();

        return $newFillerQuiz;
    }

}
