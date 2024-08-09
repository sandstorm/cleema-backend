<?php

namespace Tests\Feature;

use App\Filament\Resources\QuizQuestionsResource;
use App\Filament\Resources\QuizQuestionsResource\Pages\CreateQuizQuestions;
use App\Filament\Resources\QuizQuestionsResource\Pages\EditQuizQuestions;
use App\Filament\Resources\QuizQuestionsResource\Pages\ListQuizQuestions;
use App\Models\AdminUsers;
use App\Models\QuizAnswers;
use App\Models\QuizQuestions;
use App\Models\Regions;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use function Pest\Laravel\assertModelExists;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

describe('Quiz Questions Filament Tests', function () {

    beforeEach(function () {
        $adminUser = AdminUsers::factory()->create()->assignRole('super_admin');
        $this->actingAs($adminUser);
    });


    it('has working routes', function () {
        Livewire::test(ListQuizQuestions::class)
            ->assertSuccessful()
            ->assertSeeLivewire(ListQuizQuestions::class);

        Livewire::test(CreateQuizQuestions::class)
            ->assertSuccessful()
            ->assertSeeLivewire(CreateQuizQuestions::class);

        $quizQuestion = QuizQuestions::factory()->create();

        Livewire::test(EditQuizQuestions::class, [
            'record' => $quizQuestion->getRouteKey(),
        ])
            ->assertSuccessful()
            ->assertSeeLivewire(EditQuizQuestions::class);
    });


    it('renders Quiz Questions Page successfully', function () {
        $visibleColumns = [
            'id',
            'question',
            'is_filler',
            'region.name',
        ];

        foreach ($visibleColumns as $column) {
            Livewire::test(ListQuizQuestions::class)
                ->assertTableColumnExists($column)
                ->assertCanRenderTableColumn($column)
                ->assertTableColumnVisible($column);
        }
    });


    it('displays and sorts Quiz Questions correctly', function () {
        $regions = Regions::factory(2)->create();
        $regions = [
            Regions::factory()->create(['name' => 'b']),
            Regions::factory()->create(['name' => 'a'])
        ];

        for ($i = 0; $i <= 1; $i++) {
            QuizQuestions::factory()->create()->region()->associate($regions[$i])->save();
            QuizQuestions::factory()->create(['is_filler' => 1])->region()->associate($regions[$i])->save();
        }
        $quizQuestions = QuizQuestions::all();

        Livewire::test(ListQuizQuestions::class)
            ->assertCanSeeTableRecords($quizQuestions)
            ->sortTable('id')
            ->assertCanSeeTableRecords($quizQuestions->pluck('id', 'id')->toArray(), true)
            ->sortTable('id', 'desc')
            ->assertCanSeeTableRecords($quizQuestions->pluck('id', 'id')->sortDesc()->toArray(), true)
            ->sortTable('id', 'asc')
            ->assertCanSeeTableRecords($quizQuestions->pluck('id', 'id')->toArray(), true)
            ->sortTable('question', 'asc')
            ->assertCanSeeTableRecords($quizQuestions->sortBy('question', 0, false), true)
            ->sortTable('question', 'desc')
            ->assertCanSeeTableRecords($quizQuestions->sortBy('question', 0, true), true)
            ->sortTable('is_filler', 'asc')
            ->assertCanSeeTableRecords($quizQuestions->sortBy('is_filler', 0, false), true)
            ->sortTable('is_filler', 'desc')
            ->assertCanSeeTableRecords($quizQuestions->sortBy('is_filler', 0, true), true)
            ->sortTable('region.name', 'asc')
            ->assertCanSeeTableRecords($quizQuestions->sortBy('region.name'), true)
            ->sortTable('region.name', 'desc')
            ->assertCanSeeTableRecords($quizQuestions->sortBy('region.name', 0, true), true);
    });


    it('can create QuizQuestions', function () {
        $region = Regions::factory()->create();

        livewire(CreateQuizQuestions::class)
            ->fillForm([
                'question' => 'Is this the real life?',
                'correct_answer' => 'b',
                'explanation' => 'Is this just fantasy?',
                'region' => $region->getRouteKey(),
            ])
            ->set('data.answers', [
                ['option' => 'a', 'text' => 'Bohemian Rhapsody by Journey'],
                ['option' => 'b', 'text' => 'Bohemian Rhapsody by Queen']
            ])
            ->call('create')
            ->assertHasNoFormErrors();


        $createdQuizQuestion = QuizQuestions::query()->first();
        $this->assertModelExists($createdQuizQuestion);

        $createdQuizAnswers = QuizAnswers::query()->whereRelation('quizQuestion', 'id', '=', $createdQuizQuestion->id)->get();

        foreach ($createdQuizAnswers as $answer) {
            assertModelExists($answer);
        }
        $this->assertCount(2, $createdQuizAnswers);
    });


    it('can edit QuizQuestions', function () {
        $region1 = Regions::factory()->create();
        $region2 = Regions::factory()->create();

        $quizQuestion = QuizQuestions::factory()->create([
            'question' => 'Is this the real life?',
            'correct_answer' => 'b',
            'explanation' => 'Is this just fantasy?',
        ]);
        $quizQuestion->region()->associate($region1)->save();

        $quizAnswerA = QuizAnswers::factory()->create([
            'option' => 'a',
            'text' => 'Bohemian Rhapsody by Journey',
        ]);
        $quizAnswerA->quizQuestion()->associate($quizQuestion)->save();

        $quizAnswerB = QuizAnswers::factory()->create([
            'option' => 'b',
            'text' => 'Bohemian Rhapsody by Queen',
        ]);
        $quizAnswerB->quizQuestion()->associate($quizQuestion)->save();

        $this->get(QuizQuestionsResource::getUrl('edit', [
            'record' => $quizQuestion,
        ]))->assertSuccessful();

        $this->assertCount(2, QuizAnswers::where('quiz_question_id', '=', $quizQuestion->id)->get());

        livewire(EditQuizQuestions::class, [
            'record' => $quizQuestion->getRouteKey(),
        ])->assertFormSet([
            'question' => $quizQuestion->question,
            'correct_answer' => $quizQuestion->correct_answer,
            'explanation' => $quizQuestion->explanation,
            'region' => $region1->id,
            // Apparently, Repeaters always lead to assertFormSet to crash when checking for it
            // https://github.com/filamentphp/filament/issues/10246
            /*'data.answers' => [
                ['option' => $quizAnswerA->option, 'text' => $quizAnswerA->text],
                ['option' => $quizAnswerB->option, 'text' => $quizAnswerB->text],
            ]*/
        ])->fillForm([
            'question' => 'Should I stay or should I go?',
            'correct_answer' => 'c',
            'explanation' => "If you say that you are mine, I'll be here 'til the end of time",
            'region' => $region2->id,
        ])->set('data.answers', [
            ['option' => 'a', 'text' => 'Should I Stay Or Should I Go by Led Zeppelin'],
            ['option' => 'b', 'text' => 'Should I Stay Or Should I Go by The Killers'],
            ['option' => 'c', 'text' => 'Should I Stay Or Should I Go by The Clash'],
        ])->call('save')
            ->assertHasNoFormErrors();

        $quizAnswers = $quizQuestion->answers()->get();

        foreach ($quizAnswers as $answer) {
            assertModelExists($answer);
        }

        $this->assertCount(3, $quizAnswers);
        $this->assertCount(3, QuizAnswers::all());

        $this->assertModelMissing($quizAnswerA);
        $this->assertModelMissing($quizAnswerB);


        expect($quizQuestion->refresh())
            ->region_id->toBe($region2->id)
            ->question->toBe('Should I stay or should I go?')
            ->correct_answer->toBe('c')
            ->explanation->toBe("If you say that you are mine, I'll be here 'til the end of time")
            ->and($quizAnswers->where('option', '=', 'a')->first())
                ->text->toBe('Should I Stay Or Should I Go by Led Zeppelin')
            ->and($quizAnswers->where('option', '=', 'b')->first())
                ->text->toBe('Should I Stay Or Should I Go by The Killers')
            ->and($quizAnswers->where('option', '=', 'c')->first())
                ->text->toBe('Should I Stay Or Should I Go by The Clash');
    });


    it('can delete QuizQuestions', function () {
        $quizQuestion = QuizQuestions::factory()->create();

        $this->assertModelExists($quizQuestion);

        livewire(EditQuizQuestions::class, [
            'record' => $quizQuestion->getRouteKey(),
        ])->callAction(DeleteAction::class);

        $this->assertModelMissing($quizQuestion);
    });


});
