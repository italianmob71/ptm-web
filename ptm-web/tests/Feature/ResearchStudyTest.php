<?php

use App\Models\ResearchStudy;
use App\Models\ResearchStudyPage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
    DB::purge('sqlite');
    // Only the tables this feature and the shared navigation need. The legacy
    // slug-index migration uses MariaDB's information_schema and cannot run on SQLite.
    foreach ([
        '0001_01_01_000000_create_users_table.php',
        '0001_01_01_000008_add_security_group_to_users_table.php',
        '2026_08_13_180000_create_cochin_books_tables.php',
        '2026_08_14_010000_add_display_order_to_cochin_books.php',
        '2026_09_10_000001_create_research_studies_tables.php',
    ] as $migration) {
        (require database_path('migrations/'.$migration))->up();
    }
    $this->withoutVite();
    $this->travelTo(now()->setDate(2026, 9, 10)->setTime(12, 0));
    $this->uploadRoot = sys_get_temp_dir().'/research-study-test-'.uniqid();
    $this->app->usePublicPath($this->uploadRoot);
    $this->admin = User::factory()->create(['security_group' => 9]);
});

afterEach(function () {
    File::deleteDirectory($this->uploadRoot);
    $this->travelBack();
});

function researchStudyData(array $overrides = []): array
{
    return array_replace([
        'title' => 'Manuscript study',
        'description' => '<p>A participatory <strong>study</strong>.</p>',
        'starts_at' => '2026-09-13 12:00:00',
        'ends_at' => '2026-09-15 12:00:00',
        'application_url' => 'https://example.com/apply',
        'application_cutoff_at' => '2026-09-12 12:00:00',
        'image_path' => 'images/uploads/study.png',
    ], $overrides);
}

test('public research page preserves the introduction and shows the empty message', function () {
    $this->get(route('get-involved'))->assertOk()
        ->assertSee('Participate in a Research Study')
        ->assertSee('offers a series of participatory studies')
        ->assertSee('There are no ongoing or upcoming Research Studies. Please check again soon.');
});

test('public list includes ongoing and upcoming studies and excludes ended studies', function () {
    ResearchStudy::create(researchStudyData(['title' => 'Upcoming study']));
    ResearchStudy::create(researchStudyData([
        'title' => 'Ongoing study', 'starts_at' => now()->subDay(), 'ends_at' => now()->addDay(),
    ]));
    ResearchStudy::create(researchStudyData([
        'title' => 'Ended study', 'starts_at' => now()->subDays(3), 'ends_at' => now()->subSecond(),
    ]));
    $this->get(route('get-involved'))->assertOk()
        ->assertSeeInOrder(['Ongoing study', 'Upcoming study'])
        ->assertDontSee('Ended study')
        ->assertSee('<strong>study</strong>', false)
        ->assertSee('href="https://example.com/apply"', false)
        ->assertSee('aria-disabled="true"', false);
});

test('applications honor custom cutoffs and the 24 hour boundary without mutating the start', function () {
    $study = new ResearchStudy(researchStudyData([
        'starts_at' => now()->addHours(24),
        'application_cutoff_at' => now()->addDays(2),
    ]));
    expect($study->applicationsClosed())->toBeFalse();
    $start = $study->starts_at->toDateTimeString();
    $this->travel(1)->seconds();
    expect($study->applicationsClosed())->toBeTrue()
        ->and($study->starts_at->toDateTimeString())->toBe($start);
    $study->starts_at = now()->addDays(5);
    $study->application_cutoff_at = now();
    expect($study->applicationsClosed())->toBeTrue();
});

test('research admin routes require super admin access', function () {
    $this->get(route('admin.research-studies.index'))->assertRedirect(route('login'));
    $this->actingAs(User::factory()->create(['security_group' => 5]));
    $this->get(route('admin.research-studies.index'))->assertForbidden();
    $this->post(route('admin.research-studies.store'), [])->assertForbidden();
    $this->put(route('admin.research-studies.page.update'), [])->assertForbidden();
});

test('admin can edit the page heading and rich text introduction', function () {
    $this->actingAs($this->admin)->get(route('admin.research-studies.index'))->assertOk();
    $this->put(route('admin.research-studies.page.update'), [
        'title' => 'Join our research', 'content' => '<p>New <strong>introduction</strong>.</p>',
    ])->assertRedirect(route('admin.research-studies.index'));
    expect(ResearchStudyPage::find(1)->title)->toBe('Join our research');
    $this->get(route('get-involved'))->assertOk()
        ->assertSee('Join our research')->assertSee('<strong>introduction</strong>', false);
});

test('admin can create update replace images and delete a study', function () {
    $this->actingAs($this->admin)->get(route('admin.research-studies.create'))->assertOk();
    $this->post(route('admin.research-studies.store'), researchStudyData([
        'image' => UploadedFile::fake()->image('study.png', 500, 500),
        'application_cutoff_at' => '',
    ]))->assertSessionHasNoErrors()->assertRedirect(route('admin.research-studies.index'));
    $study = ResearchStudy::firstOrFail();
    expect($study->application_cutoff_at->toDateTimeString())->toBe('2026-09-12 12:00:00')
        ->and(is_file(public_path($study->image_path)))->toBeTrue();
    $originalPath = $study->image_path;
    $this->get(route('admin.research-studies.edit', $study))->assertOk();
    $this->put(route('admin.research-studies.update', $study), researchStudyData([
        'title' => 'Updated study', 'application_cutoff_at' => '2026-09-14 12:00:00',
    ]))->assertSessionHasNoErrors();
    expect($study->fresh()->image_path)->toBe($originalPath)
        ->and($study->fresh()->title)->toBe('Updated study')
        ->and($study->fresh()->application_cutoff_at->toDateTimeString())->toBe('2026-09-14 12:00:00');
    $this->put(route('admin.research-studies.update', $study), researchStudyData([
        'image' => UploadedFile::fake()->image('replacement.png', 500, 500),
    ]))->assertSessionHasNoErrors();
    $replacementPath = $study->fresh()->image_path;
    expect(is_file(public_path($originalPath)))->toBeFalse()
        ->and(is_file(public_path($replacementPath)))->toBeTrue();
    $this->delete(route('admin.research-studies.destroy', $study))->assertRedirect(route('admin.research-studies.index'));
    expect(ResearchStudy::count())->toBe(0)
        ->and(is_file(public_path($replacementPath)))->toBeFalse();
});

test('study validation requires an image strict date order and a safe application url', function () {
    $this->actingAs($this->admin)->post(route('admin.research-studies.store'), researchStudyData([
        'ends_at' => '2026-09-13 12:00:00', 'application_url' => 'javascript:alert(1)',
    ]))->assertSessionHasErrors(['image', 'ends_at', 'application_url']);
    $this->post(route('admin.research-studies.store'), researchStudyData([
        'image' => UploadedFile::fake()->image('wrong-size.png', 499, 500),
        'ends_at' => '2026-09-12 12:00:00',
    ]))->assertSessionHasErrors(['image', 'ends_at']);
    expect(ResearchStudy::count())->toBe(0);
});
