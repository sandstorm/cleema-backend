<?php

namespace App\Http\Resources;

use App\Models\JoinedChallenges;
use App\Models\UpUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\isEmpty;

class ChallengesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = Auth::guard('localAuth')->user();
        if(!$user){
            return ['error' => 'Authentication failed'];
        }

        $joined = $user->challengesJoined->contains('id', $this->id);
        $joinedChallenges = JoinedChallenges::where('challenge_id', '=', $this->id)->get();
        $joinedChallenge = JoinedChallenges::where('user_id', '=',$user->id)->where('challenge_id', '=', $this->id)->first();

        $usersJoined =  UpUsers::join('joined_challenges', 'joined_challenges.user_id', '=', 'up_users.id')
            ->join('challenges', 'joined_challenges.challenge_id', '=', 'challenges.id')
            ->where('joined_challenges.challenge_id', '=', $this->id)
            ->get();

        return [
            'title' => $this->title,
            'description' => $this->description ?? '',
            'interval' => $this->interval,
            'isPublic' => $this->is_public ?? false,
            'kind' => $this->kind,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'publishedAt' => $this->published_at,
            'locale' => $this->locale,
            'goalType' => $this->goal_type,
            'uuid' => $this->uuid,
            'teaserText' => $this->teaser_text,
            'region' => new RegionsResource($this->region),
            'goalMeasurement' => new GoalTypeMeasurementResource($this->goalTypeMeasurement),
            'goalSteps' => new GoalTypeStepsResource($this->goalTypeSteps),
            'joinedChallenge' => $joinedChallenge ? new JoinedChallengeResource($joinedChallenge) : null,
            'collectiveGoalAmount' => $this->collective_goal_amount,
            'collectiveProgress' => $this->getCollectiveProgress(),
            //'joinedChallenges' => isEmpty($joinedChallenges->toArray()) ? new JoinedChallengeCollection($joinedChallenges) : null,
            //'collectiveAnswers' => $this->collectiveAnswers(),
            'usersJoined' =>  new UpUsersCollection($usersJoined),
            'joined' => $joined,
            'partner' => new PartnersResource($this->partner),
            'image' => new ChallengeImage($this->image),
            //'author' => new UpUsersResource($this->author),
        ];
    }

    // maybe for future, when we need all answers
    public function collectiveAnswers() {
        $joinedChallenges = JoinedChallenges::where('challenge_id', '=', $this->id)->get();
        if (empty($joinedChallenges)) {
            return null;
        }
        $collectiveAnswers = [
            'answers' => [],
        ];
        foreach ($joinedChallenges as $joinedChallenge) {
            assert($joinedChallenge instanceof JoinedChallenges);
            $collectiveAnswers['answers'] = $joinedChallenge->answers()->get()->toArray();
            //echo (count($joinedChallenge->answers()->get()->toArray()) . ' - ');
        }

        return $collectiveAnswers;
    }

    public function getCollectiveProgress() {
        $joinedChallenges = JoinedChallenges::where('challenge_id', '=', $this->id)->get();

        if (empty($joinedChallenges)) {
            return null;
        }

        $collectiveAnswers = [];
        $collectiveSuccesses = 0;
        foreach ($joinedChallenges as $challenge) {
            assert($challenge instanceof JoinedChallenges);
            $challengeAnswers = $challenge->answers()->get();

            // If we have more than "succeeded" and "failed" as answers, we might want to send all answers in our
            // response instead of just the number of succeeded ones
            //array_push($collectiveAnswers, $challengeAnswers);

            foreach ($challengeAnswers as $answer) {
                if ($answer->answer == "succeeded") $collectiveSuccesses++;
            }
        }
        return $collectiveSuccesses;
    }
}
