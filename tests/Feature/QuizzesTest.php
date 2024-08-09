<?php

namespace Tests\Feature;

use App\Filament\Resources\QuizzesResource;
use App\Models\Regions;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\QuizzesResource\Pages\CreateQuizzes;
use App\Filament\Resources\QuizzesResource\Pages\EditQuizzes;
use App\Filament\Resources\QuizzesResource\Pages\ListQuizzes;
use App\Models\AdminUsers;
use App\Models\QuizAnswers;
use App\Models\QuizQuestions;
use App\Models\Quizzes;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

describe('Quizzes Filament Tests', function () {

    beforeEach(function () {
        $adminUser = AdminUsers::factory()->create()->assignRole('super_admin');
        $this->actingAs($adminUser);
    });


    it('has working routes', function () {
        Livewire::test(ListQuizzes::class)
            ->assertSuccessful()
            ->assertSeeLivewire(ListQuizzes::class);

        Livewire::test(CreateQuizzes::class)
            ->assertSuccessful()
            ->assertSeeLivewire(CreateQuizzes::class);

        $quiz = Quizzes::factory()->create();

        Livewire::test(EditQuizzes::class, [
            'record' => $quiz->getRouteKey()
        ])
            ->assertSuccessful()
            ->assertSeeLivewire(EditQuizzes::class);
    });


    it('renders Quizzes Page successfully', function () {
        Livewire::test(ListQuizzes::class)
            ->assertSuccessful()
            ->assertSeeLivewire(ListQuizzes::class);

        $visibleColumns = [
            'id',
            'quizQuestion.question',
            'quizQuestion.region.name',
            'date',
        ];

        foreach ($visibleColumns as $column) {
            Livewire::test(ListQuizzes::class)
                ->assertTableColumnExists($column)
                ->assertCanRenderTableColumn($column)
                ->assertTableColumnVisible($column);
        }
    });


    it('displays and sorts Quizzes correctly', function () {
        $quizQuestion1 = QuizQuestions::factory()->create();
        $quizQuestion1->region()->associate(Regions::factory()->create())->save();

        $quizQuestion2 = QuizQuestions::factory()->create();
        $quizQuestion2->region()->associate(Regions::factory()->create())->save();

        $quizzes = Quizzes::factory(5)->create();
        foreach ($quizzes as $index => $quiz) {
            if ($index % 2 == 0) {
                $quiz->quizQuestion()->associate($quizQuestion1)->save();
            } else {
                $quiz->quizQuestion()->associate($quizQuestion2)->save();
            }
            $quiz->date = Carbon::now()->subDays($index)->format('Y-m-d');
            $quiz->save();
        }

        Livewire::test(ListQuizzes::class)
            ->assertCanSeeTableRecords($quizzes);

        Livewire::test(ListQuizzes::class)
            ->sortTable('id')
            ->assertCanSeeTableRecords(Quizzes::all()->pluck('id', 'id')->toArray(), true)
            ->sortTable('id', 'desc')
            ->assertCanSeeTableRecords(Quizzes::all()->pluck('id', 'id')->sortDesc()->toArray(), true)
            ->sortTable('id', 'asc')
            ->assertCanSeeTableRecords(Quizzes::all()->pluck('id', 'id')->sort()->toArray(), true)

            ->sortTable('date', 'asc')
            ->assertCanSeeTableRecords(Quizzes::query()->orderBy('date', 'asc')->pluck('id', 'id'), true)
            ->sortTable('date', 'desc')
            ->assertCanSeeTableRecords(Quizzes::query()->orderBy('date', 'desc')->pluck('id', 'id'), true)

            ->sortTable('quizQuestion.question', 'asc')
            ->assertCanSeeTableRecords(Quizzes::query()->join(
                'quiz_questions', 'quizzes.quiz_question_id', '=', 'quiz_questions.id'
            )->orderBy('quiz_questions.question', 'asc')
                ->pluck('quizzes.id', 'quizzes.id'), true)
            ->sortTable('quizQuestion.question', 'desc')
            ->assertCanSeeTableRecords(Quizzes::query()->join(
                'quiz_questions', 'quizzes.quiz_question_id', '=', 'quiz_questions.id'
            )->orderBy('quiz_questions.question', 'desc')
                ->pluck('quizzes.id', 'quizzes.id'), true)

            ->sortTable('quizQuestion.region.name', 'asc')
            ->assertCanSeeTableRecords(Quizzes::query()->join(
                'quiz_questions', 'quizzes.quiz_question_id', '=', 'quiz_questions.id'
            )->join(
                'regions', 'quiz_questions.region_id', '=', 'regions.id'
            )->orderBy(
                'regions.name', 'asc'
            )->pluck('quizzes.id', 'quizzes.id'), true)

            ->sortTable('quizQuestion.region.name', 'desc')
            ->assertCanSeeTableRecords(Quizzes::query()->join(
                'quiz_questions', 'quizzes.quiz_question_id', '=', 'quiz_questions.id'
            )->join(
                'regions', 'quiz_questions.region_id', '=', 'regions.id'
            )->orderBy(
                'regions.name', 'desc'
            )->pluck('quizzes.id', 'quizzes.id'), true)
        ;
    });


    it('can create Quizzes', function () {
        $quizQuestion = QuizQuestions::factory()->create([
            'question' => "Is this the real life?",
            'correct_answer' => "b",
            'explanation' => "Is this just fantasy?",
        ]);

        QuizAnswers::factory()->create([
            'option' => 'a',
            'text' => 'a',
        ])->quizQuestion()->associate($quizQuestion)->save();

        QuizAnswers::factory()->create([
            'option' => 'b',
            'text' => 'b',
        ])->quizQuestion()->associate($quizQuestion)->save();

        livewire(CreateQuizzes::class)
            ->set('data.quiz_question_id', $quizQuestion->id)
            ->set('data.date', Carbon::now()->format('Y-m-d'))
            ->fillForm([
                'date' => Carbon::now()->format('Y-m-d'),
                'quiz_question_id' => $quizQuestion->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Quizzes::class, [
            'date' => Carbon::now()->format('Y-m-d'),
            'quiz_question_id' => $quizQuestion->id,
        ]);

        $createdQuiz = $quizQuestion->quizzes()->first();

        Livewire::test(ListQuizzes::class)
            ->assertCanSeeTableRecords([$createdQuiz]);
    });


    it('can edit Quizzes', function () {
        $quizQuestion = QuizQuestions::factory()->create([
            'question' => "Is this the real life?",
            'correct_answer' => "b",
            'explanation' => "Is this just fantasy?",
        ]);

        QuizAnswers::factory()->create([
            'option' => 'a',
            'text' => 'a',
        ])->quizQuestion()->associate($quizQuestion)->save();

        QuizAnswers::factory()->create([
            'option' => 'b',
            'text' => 'b',
        ])->quizQuestion()->associate($quizQuestion)->save();


        $quiz = Quizzes::factory()->create([
            'date' => Carbon::now()->toDateString(),
        ]);
        $quiz->quizQuestion()->associate($quizQuestion)->save();


        $newQuizQuestion = QuizQuestions::factory()->create([
            'question' => "Should I stay or should I go?",
            'correct_answer' => "c",
            'explanation' => "Is this just fantasy?",
        ]);
        QuizAnswers::factory()->create([
            'option' => 'a',
            'text' => 'Should I Stay Or Should I Go by Led Zeppelin',
        ])->quizQuestion()->associate($newQuizQuestion)->save();
        QuizAnswers::factory()->create([
            'option' => 'b',
            'text' => 'Should I Stay Or Should I Go by The Killers',
        ])->quizQuestion()->associate($newQuizQuestion)->save();
        QuizAnswers::factory()->create([
            'option' => 'c',
            'text' => 'Should I Stay Or Should I Go by The Clash',
        ])->quizQuestion()->associate($newQuizQuestion)->save();


        $this->get(QuizzesResource::getUrl('edit', [
            'record' => $quiz,
        ]))->assertSuccessful();

        livewire(EditQuizzes::class, [
            'record' => $quiz->getRouteKey(),
        ])->assertFormSet([
            'date' => Carbon::make($quiz->date)->toDateString(),
            'quiz_question_id' => $quiz->quizQuestion->getKey(),
        ])->fillForm([
            'date' => Carbon::now()->addDay()->toDateString(),
            'quiz_question_id' => $newQuizQuestion->getRouteKey(),
        ])->call('save')
            ->assertHasNoFormErrors();

        expect($quiz->refresh())
            ->quiz_question_id->toBeNumeric()
            ->quiz_question_id->toBe($newQuizQuestion->getRouteKey())
            ->and(Carbon::make($quiz->date)->toDateString())
            ->toBe(Carbon::now()->addDay()->toDateString());
    });


    it('can delete Quizzes', function () {
        $quizQuestion = QuizQuestions::factory()->create([
            'question' => "Is this the real life?",
            'correct_answer' => "b",
            'explanation' => "Is this just fantasy?",
        ]);

        $quiz = Quizzes::factory()->create([
            'date' => Carbon::now()->format('Y-m-d'),
        ]);
        $quiz->quizQuestion()->associate($quizQuestion)->save();

        $this->assertModelExists($quiz);

        livewire(EditQuizzes::class, [
            'record' => $quiz->getRouteKey(),
        ])->callAction(DeleteAction::class);

        $this->assertModelMissing($quiz);
    });


});
