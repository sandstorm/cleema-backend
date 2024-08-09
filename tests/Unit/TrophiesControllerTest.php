<?php


namespace Tests\Unit;

use App\Http\Resources\TrophiesCollection;
use App\Models\AdminUsers;
use App\Models\Challenges;
use App\Models\JoinedChallenges;
use App\Models\JoinedChallengesAnswers;
use App\Models\Projects;
use App\Models\QuizAnswers;
use App\Models\QuizQuestions;
use App\Models\Quizzes;
use App\Models\Surveys;
use \App\Models\UpUsers;
use \App\Models\Trophies;
use \App\Http\Controllers\TrophiesController;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Event\TestData\TestData;
use Tests\TestCase;
use Carbon\Carbon;
use function Pest\Laravel\actingAs;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class TrophiesControllerTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    public function test_awardTrophy(): void
    {
        $user = UpUsers::factory()->create(['referral_count' => 3]);
        assert($user instanceof UpUsers);

        $userTrophies = $user->trophies()->get();

        assertCount(0, $userTrophies);

        $trophy = Trophies::factory()->create();
        assert($trophy instanceof Trophies);

        TrophiesController::awardTrophy($user, $trophy);

        $userTrophies = $user->trophies()->get();

        assertCount(1, $user->trophies()->get());

        assertEquals($trophy->title, $userTrophies[0]->title);
    }


    public function test_checkReferralCountTrophies(): void
    {
        $user = UpUsers::factory()->create(['referral_count' => 1]);

        $trophy = Trophies::factory()->create(['amount' => 1, 'kind' => 'referral-count']);
        assertCount(0, $user->trophies()->get());

        TrophiesController::checkReferralCountTrophies($user);

        assertCount(1, $user->trophies()->get());
    }


    public function test_checkAllUsersAmountOfChallengesParticipated(): void
    {
        $user = UpUsers::factory()->create();
        $challenge = Challenges::factory()->create(['start_date' => '2023-03-15', 'end_date' => '2023-03-25']);
        $joinedChallenge = JoinedChallenges::factory()->for($user, 'user')->for($challenge, 'challenge')->create();
        $joinedChallengeAnswers = JoinedChallengesAnswers::factory()
            ->count(10)
            ->for($joinedChallenge, 'joinedChallenge')
            ->state(new Sequence(
                fn(Sequence $sequence) => ['day_index' => $sequence->count],
            ))
            ->create();

        TrophiesController::checkAllUsersAmountOfChallengesParticipated();
        $userTrophies = $user->trophies()->get();
        assertCount(0, $userTrophies);

        $trophy = Trophies::factory()->create(['amount' => 1, 'kind' => 'challenge-participation']);
        TrophiesController::checkAllUsersAmountOfChallengesParticipated();

        $userTrophies = $user->trophies()->get();
        assertCount(1, $userTrophies);

    }


    public function test_checkAllPublicChallengeTrophies(): void
    {
        $user = UpUsers::factory()->create();
        for ($i = 0; $i < 4; $i++) {
            $challenge = Challenges::factory()->create(['start_date' => '2023-03-15', 'end_date' => '2023-03-25', 'kind' => 'partner']);
            $challenge->joinedUsers()->attach($user);
            $joinedChallenge = JoinedChallenges::where('user_id', '=', $user->id)->where('challenge_id', '=', $challenge->id)->first();
            assert($joinedChallenge instanceof JoinedChallenges);
            $joinedChallengeAnswers = JoinedChallengesAnswers::factory()
                ->count(10)
                ->for($joinedChallenge, 'joinedChallenge')
                ->state(new Sequence(
                    fn(Sequence $sequence) => ['day_index' => $sequence->count],
                ))
                ->create();
            $trophy = Trophies::factory()->for($challenge, 'challenge')->create(['amount' => $i, 'kind' => 'challenge-participation']);
        }
        assertCount(0, $user->trophies()->get());

        try {
            TrophiesController::checkAllPublicChallengeTrophies();
            assertCount(4, $user->trophies()->get());
        } catch (\Exception $e) {
            echo('EXCEPTION: ' . $e);
            self::fail();
        }

        $trophy = Trophies::factory()->for($challenge, 'challenge')->create(['amount' => 20, 'kind' => 'challenge-participation']);
        TrophiesController::checkAllPublicChallengeTrophies();
        assertCount(4, $user->trophies()->get());

    }

    public function test_checkAccountAgeTrophies(): void
    {
        $user = UpUsers::factory()->create();
        $trophy1 = Trophies::factory()->create(['amount' => 1, 'kind' => 'account-age']);
        $user->created_at = Carbon::now()->subMonths(5);
        $user->save();
        $userTrophies = $user->trophies()->get();
        assertCount(0, $userTrophies);

        TrophiesController::checkAccountAgeTrophies();
        $userTrophies = $user->trophies()->get();
        assertCount(1, $userTrophies);
        assertTrue($userTrophies->contains($trophy1));

        $trophy2 = Trophies::factory()->create(['amount' => 3, 'kind' => 'account-age']);

        TrophiesController::checkAccountAgeTrophies(true);

        $userTrophies = $user->trophies()->get();
        assertCount(2, $userTrophies);
        assertTrue($userTrophies->contains($trophy1));
        assertTrue($userTrophies->contains($trophy2));
    }

    public function test_getUsersNewTrophies()
    {
        $user = UpUsers::factory()->create();
        $trophy = Trophies::factory()->create(['amount' => 1, 'kind' => 'account-age']);
        $user->trophies()->attach($trophy, ['notified' => 0]);

        $this->actingAs($user, 'localAuth');

        assertCount(1, $user->trophies()->wherePivot('notified', '=', 0)->get());
        assertCount(0, $user->trophies()->wherePivot('notified', '=', 1)->get());

        $trophiesCollection = TrophiesController::getUsersNewTrophies();

        assertTrue($trophiesCollection instanceof TrophiesCollection);
        assertCount(1, $trophiesCollection);
        assertCount(1, $user->trophies()->wherePivot('notified', '=', 1)->get());
    }

    public function test_getUsersTrophies()
    {
        $adminUser = AdminUsers::factory()->create();

        $user = UpUsers::factory()->create();
        $this->actingAs($user, 'localAuth');
        $userTrophies = TrophiesController::getUsersTrophies();
        assertCount(0, $userTrophies);

        $this->actingAs($adminUser, 'localAuth');

        $trophies = Trophies::factory()->count(4)->create();
        $user->trophies()->attach($trophies);

        $this->actingAs($user, 'localAuth');
        $userTrophies = TrophiesController::getUsersTrophies();
        assertCount(4, $userTrophies);
    }

    public function test_checkQuizTrophies()
    {
        $adminUser = AdminUsers::factory()->create();
        $this->actingAs($adminUser, 'localAuth');

        $user = UpUsers::factory()->create();

        assertCount(0, $user->trophies()->get());

        $quizQuestion = QuizQuestions::factory()
            ->create([
                "question" => "testQuestion",
                "correct_answer" => "b",
                "explanation" => "testExplanation",
            ]);
        assertCount(0, $quizQuestion->answers()->get());

        for ($i = 0; $i < 4; $i++) {
            $letters = range('a', 'd');
            QuizAnswers::factory()->for($quizQuestion, 'quizQuestion')->create([
                "option" => $letters[$i],
                "text" => "exampleText",
            ]);
        }
        assertCount(4, $quizQuestion->answers()->get());

        $this->actingAs($adminUser);

        $quiz = Quizzes::factory()->create();
        $quiz->quizQuestion()->associate($quizQuestion);

        $quiz->responses()->attach($user, [
            "answer" => "b",
            "date" => Carbon::now(),
        ]);

        $quiz->save();

        $this->actingAs($user, 'localAuth');

        TrophiesController::checkQuizTrophies();
        assertCount(0, $user->trophies()->get());

        $this->actingAs($adminUser);

        Trophies::factory()->create([
            "title" => "QuizTrophy",
            "kind" => "quiz-correct-answers",
            "amount" => 1,
        ]);

        $this->actingAs($user, 'localAuth');

        TrophiesController::checkQuizTrophies();
        assertCount(1, $user->trophies()->get());

        $this->actingAs($adminUser);

        Trophies::factory()->create([
            "title" => "QuizTrophy",
            "kind" => "quiz-correct-answers",
            "amount" => 1,
        ]);

        $this->actingAs($user, 'localAuth');

        TrophiesController::checkQuizTrophies();
        assertCount(2, $user->trophies()->get());
    }

    public function test_checkSurveyTrophies()
    {
        $user = UpUsers::factory()->create([
            "accepts_surveys" => true,
        ]);

        $adminUser = AdminUsers::factory()->create();
        $this->actingAs($adminUser);
        $surveys = Surveys::factory()->count(5)->create([
            "finished" => true,
            "trophy_processed" => false,
        ]);

        $user->enteredSurveys()->attach($surveys);

        Trophies::factory()->create([
            "kind" => 'survey-participation',
            "amount" => 1,
        ]);

        Trophies::factory()->create([
            "kind" => 'survey-participation',
            "amount" => 5,
        ]);

        $this->actingAs($user, 'localAuth');

        assertCount(0, $user->trophies()->get());

        TrophiesController::checkSurveyTrophies();
        assertCount(2, $user->trophies()->get());

        TrophiesController::checkSurveyTrophies();
        assertCount(2, $user->trophies()->get());
    }

    public function test_checkProjectTrophies()
    {
        $adminUser = AdminUsers::factory()->create();
        $this->actingAs($adminUser);

        $user = UpUsers::factory()->create([
            "accepts_surveys" => true,
        ]);
        TrophiesController::checkProjectTrophies();
        assertCount(0, $user->projectsJoined()->get());

        $projects = Projects::factory()->count(5)->create();
        $user->projectsJoined()->attach($projects);
        assertCount(5, $user->projectsJoined()->get());

        Trophies::factory()->create([
            "kind" => 'project-participation',
            "amount" => 1,
        ]);

        $this->actingAs($user, 'localAuth');

        TrophiesController::checkProjectTrophies();
        assertCount(1, $user->trophies()->get());

        $this->actingAs($adminUser);
        Trophies::factory()->create([
            "kind" => 'project-participation',
            "amount" => 5,
        ]);

        $this->actingAs($user, 'localAuth');
        TrophiesController::checkProjectTrophies();
        assertCount(2, $user->trophies()->get());
    }

}

