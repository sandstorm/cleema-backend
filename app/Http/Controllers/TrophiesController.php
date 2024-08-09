<?php

namespace App\Http\Controllers;

use App\Console\Kernel;
use App\Http\Resources\FilesResource;
use App\Http\Resources\TrophiesCollection;
use App\Http\Resources\TrophiesResource;
use App\Models\Challenges;
use App\Models\JoinedChallenges;
use App\Models\JoinedChallengesAnswers;
use App\Models\Quizzes;
use App\Models\Surveys;
use App\Models\Trophies;
use App\Models\UpUsers;
use http\Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use function Pest\Laravel\instance;

class TrophiesController extends Controller
{
    /**
     * checks user trophies the user has not been notified for yet, sets notified and returns them
     * @return TrophiesCollection|string
     */
    static function getUsersNewTrophies(): TrophiesCollection|string
    {
        $user = Auth::guard('localAuth')->user();
        if (!$user) {
            return response()->json(Controller::getApiErrorMessage('Authentication failed'), 400);
        }
        assert($user instanceof UpUsers);
        $trophies = $user->trophies()->wherePivot('notified', '=', '0')->get();
        foreach ($trophies as $trophy) {
            $user->trophies()->updateExistingPivot($trophy->id, ['notified' => 1]);
        }
        return new TrophiesCollection($trophies);
    }

    /**
     * checks users quiz trophies not awarded yet, awards them and returns all user trophies
     * @return TrophiesCollection|string
     */
    static function getUsersTrophies(): TrophiesCollection|string
    {
        $user = Auth::guard('localAuth')->user();
        if (!$user) {
            return response()->json(Controller::getApiErrorMessage('Authentication failed'));
        }
        assert($user instanceof UpUsers);
        self::checkQuizTrophies();
        return new TrophiesCollection($user->trophies()->get());
    }

    static function fetchOne(string $uuid)
    {
        $user = Auth::guard('localAuth')->user();
        if (!$user) {
            return response()->json(Controller::getApiErrorMessage('Authentication failed'));
        }
        assert($user instanceof UpUsers);

        $trophy = Trophies::where('uuid', '=', $uuid)->first();
        if (!$trophy) {
            return response()->json(Controller::getApiErrorMessage('Trophy not found'), 404);
        }
        assert($trophy instanceof Trophies);

        return new TrophiesResource($trophy);
    }

    /**
     * awards a specific trophy to a user
     * @return void
     */
    static function awardTrophy($user, $trophy): void
    {
        assert($user instanceof UpUsers);
        assert($trophy instanceof Trophies);

        $user->trophies()->attach($trophy, ['date' => Carbon::now(), 'notified' => 0]);
    }

    /**
     * checks if a trophy must be awarded to a user after answering a quiz
     * @return bool|null
     */
    static function checkQuizTrophies(): bool|null
    {
        $user = Auth::guard('localAuth')->user();
        if (!$user) {
            return null;
        }
        assert($user instanceof UpUsers);
        $quizResponses = $user->quizResponses()->get();
        $countCorrect = 0;
        foreach ($quizResponses as $response) {
            if ($response->pivot->answer == $response->quizQuestion()->first()->correct_answer) {
                $countCorrect += 1;
            }
        }
        $trophies = Trophies::where('kind', 'quiz-correct-answers')->get();
        $earnedTrophies = $user->trophies()->get();
        $trophyAwarded = false;
        foreach ($trophies as $trophy) {
            if ($countCorrect >= $trophy->amount && !$earnedTrophies->find($trophy->id)) {
                TrophiesController::awardTrophy($user, $trophy);
            }
            $trophyAwarded = true;
        }
        return $trophyAwarded;
    }

    /**
     * checks if a trophy must be awarded when:
     *      - user participated at a certain part of surveys which are finished
     * @return
     */
    // TODO when does this get called??
    // Maybe after a user completes a survey?
    static function checkSurveyTrophies()
    {
        $user = Auth::guard('localAuth')->user();
        if (!$user) {
            return self::getApiErrorMessage('Authentication failed');
        }
        assert($user instanceof UpUsers);
        $trophies = Trophies::where('kind', 'survey-participation')->get();
        $earnedTrophies = $user->trophies()->get();
        $surveysParticipated = $user->enteredSurveys()->where('finished', '=', true)->count();
        $trophiesAwarded = false;
        foreach ($trophies as $trophy) {
            if ($surveysParticipated >= $trophy->amount && !$earnedTrophies->find($trophy->id)) {
                self::awardTrophy($user, $trophy);
                $trophiesAwarded = true;
            }
        }
        return $trophiesAwarded;
    }

    /**
     * checks of a trophy must be awarded when:
     *      - user participated a challenge --> he gets a the partner trophy
     *      - check all challenge trophies
     * @return void
     */
    static function checkAllPublicChallengeTrophies(): void
    {
        $challenges = Challenges::query()->where('trophy_processed', '=', false)
            ->whereIn('kind', ['partner', 'collective'])
            ->where('end_date', '<', Carbon::now())
            ->get();

        foreach ($challenges as $challenge) {
            assert($challenge instanceof Challenges);
            $trophies = Trophies::where('challenge_id', '=', $challenge->id)->get();

            if (!empty($trophies)) {
                foreach ($challenge->joinedUsers()->distinct()->get() as $user) {
                    assert($user instanceof UpUsers);

                    $earnedTrophies = $user->trophies()->get()->pluck('uuid', 'id');

                    foreach ($trophies as $trophy) {
                        assert($trophy instanceof Trophies);

                        if (!$earnedTrophies->search($trophy->uuid)) {
                            $amount = $trophy->amount != null ? $trophy->amount : 1;
                            $userChallenge = JoinedChallenges::where('user_id', '=', $user->id)->where('challenge_id', '=', $challenge->id)->first();
                            $successfulAnswers = $userChallenge->answers()->where('answer', '=', 'succeeded')->get()->count();

                            if ($successfulAnswers >= $amount) {
                                self::awardTrophy($user, $trophy);
                            }
                        }
                    }
                }
            }
            $challenge->update(['trophy_processed' => true]);
        }
    }

    /**
     *
     * @return void
     * */
    static function checkAllUsersAmountOfChallengesParticipated(): void
    {
        $users = UpUsers::all();
        $trophies = Trophies::query()->where('kind', '=', 'challenge-participation')->whereNull('challenge_id')->get();
        foreach ($users as $user) {
            $earnedTrophies = $user->trophies()->get()->pluck('uuid', 'id');

            $unearnedTrophies = array_diff($trophies->pluck('uuid', 'id')->toArray(), $earnedTrophies->toArray());
            if (!empty($unearnedTrophies)) {
                $userChallenges = JoinedChallenges::where('user_id', '=', $user->id)->get();
                $successfulChallenges = 0;
                foreach ($userChallenges as $joinedChallenge) {
                    $challenge = $joinedChallenge->challenge()->first();

                    // We check if the JoinedChallenge has a Challenge, because we had an issue where apparently the
                    // JC's Challenge was deleted, which just sets the JC's challenge_id to null, causing this to crash
                    if ($challenge) {
                        $successful = $joinedChallenge->answers()->where('answer', '=', 'succeeded')->get()->count();

                        $maxSuccessful = self::getMaxAnswers($challenge);
                        $minSuccessful = ceil($maxSuccessful / 2);

                        if ($successful >= $minSuccessful) {
                            $successfulChallenges++;
                        }
                    }
                }
                foreach ($unearnedTrophies as $trophyUuid) {
                    $trophy = Trophies::where('uuid', '=', $trophyUuid)->first();
                    $amount = $trophy->amount;
                    if ($successfulChallenges >= $amount) {
                        self::awardTrophy($user, $trophy);
                    }
                }
            }
        }
    }

    static function getMaxAnswers($challenge)
    {
        // get max answers
        $start = $challenge->start_date;
        $end = $challenge->end_date;
        if ($challenge->interval == 'weekly') {
            $maxAnswers = $end->diffInWeeks($start);
        } else {
            $maxAnswers = $end->diffInDays($start);
        }
        return $maxAnswers;
    }


    /**
     * Checked when a user joins a Project
     * checks if a trophy must be awarded when:
     *      - project type trophies are earned
     * @return array|bool
     */
    static function checkProjectTrophies()
    {
        $user = Auth::guard('localAuth')->user();
        if (!$user) {
            return self::getApiErrorMessage('Authentication failed');
        }
        assert($user instanceof UpUsers);
        $trophies = Trophies::where('kind', 'project-participation')->get();
        $projectsJoined = $user->projectsJoined()->count();
        $earnedTrophies = $user->trophies()->get();
        $trophyAwarded = false;
        foreach ($trophies as $trophy) {
            if ($projectsJoined >= $trophy->amount && !$earnedTrophies->find($trophy->id)) {
                self::awardTrophy($user, $trophy);
                $trophyAwarded = true;
            }
        }
        return $trophyAwarded;
    }

    /**
     * TODO call when referring cleema to a friend
     * checks if a trophy must be awarded when:
     *      - referralCount type trophies are earned
     * @return void
     */
    static function checkReferralCountTrophies($user): void
    {
        $trophies = Trophies::where('kind', 'referral-count')->get();
        assert($user instanceof UpUsers);

        $earnedTrophies = $user
            ->trophies()
            ->distinct()
            ->pluck('trophy_id')
            ->toArray();
        foreach ($trophies as $trophy) {
            assert($trophy instanceof Trophies);
            if ($user->referral_count >= $trophy->amount && !in_array($trophy->id, $earnedTrophies)) {
                self::awardTrophy($user, $trophy);
            }
        }
    }

    /**
     * In
     * @return void @see Kernel überprüft für alle eingeloggten Nutzer
     * checks if a trophy must be awarded when:
     *      - account age type trophies are earned
     */
    static function checkAccountAgeTrophies(): void
    {
        $users = UpUsers::where('is_anonymous', '=', false)->get();
        foreach ($users as $user) {
            assert($user instanceof UpUsers);
            $ageInMonths = Carbon::now()->diffInMonths($user->created_at);
            $trophies = Trophies::where('kind', 'account-age')->get();
            $earnedTrophies = $user->trophies()->distinct()->pluck('id')->filter();
            foreach ($trophies as $trophy) {
                assert($trophy instanceof Trophies);
                if ($ageInMonths >= $trophy->amount && !$earnedTrophies->contains($trophy->id)) {
                    self::awardTrophy($user, $trophy);
                }
            }
        }
    }
}
