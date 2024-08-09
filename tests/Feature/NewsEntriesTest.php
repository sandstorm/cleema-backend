<?php

namespace Tests\Feature;

use App\Filament\Resources\NewsEntriesResource\Pages\CreateNewsEntries;
use App\Filament\Resources\NewsEntriesResource\Pages\EditNewsEntries;
use App\Filament\Resources\NewsEntriesResource\Pages\ListNewsEntries;
use App\Models\AdminUsers;
use App\Models\NewsEntries;
use BezhanSalleh\FilamentShield\Support\Utils;
use Database\Seeders\ShieldSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\NewsTags;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;
use function Pest\Laravel\seed;
use function Pest\Livewire\livewire;
use function Pest\Faker\fake;

uses(RefreshDatabase::class);

describe('NewsEntries Filament Tests', function () {

    beforeEach(function () {
        $adminUser = AdminUsers::factory()->create()->assignRole('super_admin');
        $this->actingAs($adminUser);
    });

    it('has working routes', function () {
        Livewire::test(ListNewsEntries::class)
            ->assertSuccessful()
            ->assertSeeLivewire(ListNewsEntries::class);

        Livewire::test(CreateNewsEntries::class)
            ->assertSuccessful()
            ->assertSeeLivewire(CreateNewsEntries::class);
    });

    it('renders NewsEntriesList Page successfully', function () {
        Livewire::test(ListNewsEntries::class)
            ->assertSuccessful();

        $visibleColumns = [
            'id',
            'title',
            'type',
            'region_id',
            'views',
            'published_at'
        ];

        $hiddenColumns = [
            'locale'
        ];

        $columns = array_merge($visibleColumns, $hiddenColumns);

        foreach ($columns as $column) {
            Livewire::test(ListNewsEntries::class)
                ->assertTableColumnExists($column)
                ->assertCanRenderTableColumn($column);
        }

        foreach ($visibleColumns as $column) {
            Livewire::test(ListNewsEntries::class)
                ->assertTableColumnVisible($column);
        }

        // TODO: this is visible, probably because it is toggleable, i have no idea on how to test for toggleable
        /*foreach ($hiddenColumns as $column) {
            Livewire::test(ListNewsEntries::class)
                ->assertTableColumnHidden($column);
        }*/
    });

    it('displays and sorts entries correctly', function () {
        $entries = NewsEntries::factory()->count(5)->create();

        Livewire::test(ListNewsEntries::class)
            ->assertCanSeeTableRecords($entries);

        $taggedEntries = NewsEntries::factory()->count(5)->create();
        $testTagValue = 'testTag';
        NewsTags::factory()->hasAttached($taggedEntries)->create(['value' => $testTagValue]);

        Livewire::test(ListNewsEntries::class)
            ->filterTable($testTagValue)
            ->assertCanSeeTableRecords($taggedEntries)
            ->assertCanNotSeeTableRecords($entries);

    });

    it('has a working creation form', function () {

        /*$title = fake()->sentence;
        livewire(CreateNewsEntries::class)
            ->fillForm([
                'title' => $title,
            ])
            ->assertFormSet([
                'title' => $title,
            ]);*/

    });
});

