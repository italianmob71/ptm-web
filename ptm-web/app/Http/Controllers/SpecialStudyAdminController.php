<?php

namespace App\Http\Controllers;

use App\Models\Pdf;
use App\Models\SpecialStudy;
use App\Models\SpecialStudyPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SpecialStudyAdminController extends Controller
{
    public function index()
    {
        return view('admin.special-studies.index', [
            'title' => 'Special Studies',
            'page' => SpecialStudyPage::findOrFail(1),
            'studies' => SpecialStudy::with('pdf')->orderBy('display_order')->orderBy('id')->get(),
            'pdfs' => Pdf::whereNotIn('id', SpecialStudy::select('pdf_id'))->orderBy('title')->orderBy('filename')->get(),
        ]);
    }

    public function updatePage(Request $request)
    {
        SpecialStudyPage::findOrFail(1)->update($request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]));

        return redirect()->route('admin.special-studies.index')->with('status', 'Special Studies page updated.');
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            SpecialStudyPage::whereKey(1)->lockForUpdate()->firstOrFail();
            $data = $request->validate([
                'pdf_id' => ['required', 'integer', Rule::exists('pdfs', 'id')->whereNull('deleted_at'), 'unique:special_studies,pdf_id'],
            ]);
            SpecialStudy::create($data + ['display_order' => (SpecialStudy::max('display_order') ?? 0) + 1]);
        });

        return redirect()->route('admin.special-studies.index')->with('status', 'PDF added to Special Studies.');
    }

    public function move(Request $request, SpecialStudy $study)
    {
        $data = $request->validate(['direction' => ['required', 'in:up,down']]);
        DB::transaction(function () use ($study, $data) {
            SpecialStudyPage::whereKey(1)->lockForUpdate()->firstOrFail();
            $studies = SpecialStudy::orderBy('display_order')->orderBy('id')->get();
            $index = $studies->search(fn ($item) => $item->id === $study->id);
            abort_if($index === false, 404);
            $target = $index + ($data['direction'] === 'up' ? -1 : 1);
            if ($target < 0 || $target >= $studies->count()) {
                return;
            }
            $other = $studies[$target];
            $studies[$target] = $studies[$index];
            $studies[$index] = $other;
            foreach ($studies as $position => $item) {
                $item->update(['display_order' => $position + 1]);
            }
        });

        return redirect()->route('admin.special-studies.index')->with('status', 'Download order updated.');
    }

    public function destroy(SpecialStudy $study)
    {
        DB::transaction(function () use ($study) {
            SpecialStudyPage::whereKey(1)->lockForUpdate()->firstOrFail();
            $study->delete();
        });

        return redirect()->route('admin.special-studies.index')->with('status', 'Removed from Special Studies. The PDF remains in the library.');
    }
}
