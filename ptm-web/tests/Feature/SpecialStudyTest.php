<?php

use App\Models\Pdf;
use App\Models\SpecialStudy;
use App\Models\SpecialStudyPage;
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
        '0001_01_01_000017_create_pdfs_table.php',
        '2026_09_10_000002_create_special_studies_tables.php',
    ] as $migration) {
        (require database_path('migrations/'.$migration))->up();
    }
    $this->withoutVite();
    $this->travelTo(now()->setDate(2026, 9, 10)->setTime(12, 0));
    $this->uploadRoot = sys_get_temp_dir().'/special-study-test-'.uniqid();
    $this->app->usePublicPath($this->uploadRoot);
    $this->admin = User::factory()->create(['security_group' => 9]);
});

afterEach(function () {
    File::deleteDirectory($this->uploadRoot);
    $this->travelBack();
});

function specialStudyPdf(string $title): Pdf
{
    return Pdf::create([
        'title' => $title, 'filename' => $title.'.pdf',
        'slug' => $title, 'path' => 'pdfs/'.$title.'.pdf',
    ]);
}

test('special studies page preserves its content and renders ordered PDF downloads', function () {
    $first = specialStudyPdf('First study');
    $second = specialStudyPdf('Second study');
    $deleted = specialStudyPdf('Deleted study');
    SpecialStudy::create(['pdf_id' => $second->id, 'display_order' => 2]);
    SpecialStudy::create(['pdf_id' => $first->id, 'display_order' => 1]);
    SpecialStudy::create(['pdf_id' => $deleted->id, 'display_order' => 3]);
    $deleted->delete();
    $this->get(route('special-studies'))->assertOk()
        ->assertSee('Special studies are everything Biblical')
        ->assertSee('Special Studies for Download')
        ->assertSeeInOrder(['First study', 'Second study'])
        ->assertSee('viewBox="0 0 32 40"', false)
        ->assertSee('href="'.$first->url.'"', false)
        ->assertDontSee('Deleted study');
});

test('special studies admin can edit heading and rich text', function () {
    $this->actingAs($this->admin)->get(route('admin.special-studies.index'))->assertOk();
    $this->put(route('admin.special-studies.page.update'), [
        'title' => 'Our special studies', 'description' => '<p>New <strong>description</strong>.</p>',
    ])->assertSessionHasNoErrors()->assertRedirect(route('admin.special-studies.index'));
    expect(SpecialStudyPage::find(1)->title)->toBe('Our special studies');
    $this->get(route('special-studies'))->assertOk()->assertSee('Our special studies')
        ->assertSee('<strong>description</strong>', false);
    $this->put(route('admin.special-studies.page.update'), ['title' => '', 'description' => ''])
        ->assertSessionHasErrors(['title', 'description']);
});

test('special studies admin can select reorder and remove library PDFs', function () {
    $this->actingAs($this->admin);
    $pdfs = collect(['Alpha', 'Beta', 'Gamma'])->map(fn ($title) => specialStudyPdf($title));
    foreach ($pdfs as $pdf) {
        $this->post(route('admin.special-studies.store'), ['pdf_id' => $pdf->id])->assertSessionHasNoErrors();
    }
    $this->get(route('admin.special-studies.index'))->assertOk()->assertSee('Alpha');
    $first = SpecialStudy::where('pdf_id', $pdfs[0]->id)->firstOrFail();
    $this->put(route('admin.special-studies.move', $first), ['direction' => 'up'])->assertSessionHasNoErrors();
    expect(SpecialStudy::orderBy('display_order')->pluck('pdf_id')->all())->toBe($pdfs->pluck('id')->all());
    $this->put(route('admin.special-studies.move', $first), ['direction' => 'down'])->assertSessionHasNoErrors();
    expect(SpecialStudy::orderBy('display_order')->pluck('pdf_id')->all())->toBe([$pdfs[1]->id, $pdfs[0]->id, $pdfs[2]->id]);
    $this->put(route('admin.special-studies.move', $first), ['direction' => 'up'])->assertSessionHasNoErrors();
    $this->get(route('special-studies'))->assertOk()->assertSeeInOrder(['Alpha', 'Beta', 'Gamma']);
    $this->delete(route('admin.special-studies.destroy', $first))->assertRedirect(route('admin.special-studies.index'));
    expect(SpecialStudy::count())->toBe(2)->and(Pdf::count())->toBe(3);
});

test('special studies reject duplicate missing and deleted PDFs and invalid directions', function () {
    $this->actingAs($this->admin);
    $pdf = specialStudyPdf('Existing');
    $study = SpecialStudy::create(['pdf_id' => $pdf->id, 'display_order' => 1]);
    $this->post(route('admin.special-studies.store'), ['pdf_id' => $pdf->id])->assertSessionHasErrors('pdf_id');
    $this->post(route('admin.special-studies.store'), ['pdf_id' => 999])->assertSessionHasErrors('pdf_id');
    $deleted = specialStudyPdf('Removed');
    $deleted->delete();
    $this->post(route('admin.special-studies.store'), ['pdf_id' => $deleted->id])->assertSessionHasErrors('pdf_id');
    $this->put(route('admin.special-studies.move', $study), ['direction' => 'sideways'])->assertSessionHasErrors('direction');
});

test('special studies admin routes are restricted to super admins', function () {
    $this->get(route('admin.special-studies.index'))->assertRedirect(route('login'));
    $this->actingAs(User::factory()->create(['security_group' => 5]));
    $pdf = specialStudyPdf('Restricted');
    $study = SpecialStudy::create(['pdf_id' => $pdf->id, 'display_order' => 1]);
    $this->get(route('admin.special-studies.index'))->assertForbidden();
    $this->post(route('admin.special-studies.store'), ['pdf_id' => $pdf->id])->assertForbidden();
    $this->put(route('admin.special-studies.page.update'), [])->assertForbidden();
    $this->put(route('admin.special-studies.move', $study), ['direction' => 'up'])->assertForbidden();
    $this->delete(route('admin.special-studies.destroy', $study))->assertForbidden();
});

test('shared PDF upload prefills category and returns to the correct admin screen', function () {
    $this->actingAs($this->admin);
    $this->get(route('admin.pdfs.create', ['from' => 'special-studies']))->assertOk()
        ->assertSee('value="Special Studies"', false)->assertSee('name="from" value="special-studies"', false);
    $this->from(route('admin.pdfs.create', ['from' => 'special-studies']))
        ->post(route('admin.pdfs.store'), ['from' => 'special-studies', 'pdf_type' => 'upload', 'category' => 'Special Studies'])
        ->assertSessionHasErrors('pdf_file')->assertSessionHasInput('from', 'special-studies');
    $this->post(route('admin.pdfs.store'), [
        'from' => 'special-studies', 'pdf_type' => 'upload', 'category' => 'Special Studies', 'title' => 'Uploaded study',
        'pdf_file' => UploadedFile::fake()->createWithContent('uploaded.pdf', "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF"),
    ])->assertSessionHasNoErrors()->assertRedirect(route('admin.special-studies.index'));
    $pdf = Pdf::firstOrFail();
    expect($pdf->category)->toBe('Special Studies')->and(is_file(public_path($pdf->path)))->toBeTrue();
    $this->get(route('admin.special-studies.index'))->assertOk()->assertSee('Uploaded study');
    $this->get(route('admin.pdfs.create'))->assertOk()->assertDontSee('name="from"', false);
    $this->post(route('admin.pdfs.store'), [
        'pdf_type' => 'upload', 'from' => 'https://example.com',
        'pdf_file' => UploadedFile::fake()->createWithContent('normal.pdf', "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF"),
    ])->assertSessionHasNoErrors()->assertRedirect(route('admin.pdfs.index'));
});
