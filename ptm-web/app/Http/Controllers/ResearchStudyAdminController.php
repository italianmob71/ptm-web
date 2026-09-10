<?php

namespace App\Http\Controllers;

use App\Models\ResearchStudy;
use App\Models\ResearchStudyPage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ResearchStudyAdminController extends Controller
{
    public function index()
    {
        return view('admin.research-studies.index', [
            'title' => 'Research Studies',
            'page' => ResearchStudyPage::findOrFail(1),
            'studies' => ResearchStudy::orderByDesc('starts_at')->paginate(25),
        ]);
    }

    public function updatePage(Request $request)
    {
        ResearchStudyPage::findOrFail(1)->update($request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]));

        return redirect()->route('admin.research-studies.index')->with('status', 'Research Studies page updated.');
    }

    public function create()
    {
        return view('admin.research-studies.form', [
            'title' => 'Add Research Study',
            'study' => new ResearchStudy(),
        ]);
    }

    public function store(Request $request)
    {
        $this->saveStudy($request, new ResearchStudy());

        return redirect()->route('admin.research-studies.index')->with('status', 'Research study created.');
    }

    public function edit(ResearchStudy $study)
    {
        return view('admin.research-studies.form', ['title' => 'Edit Research Study', 'study' => $study]);
    }

    public function update(Request $request, ResearchStudy $study)
    {
        $this->saveStudy($request, $study);

        return redirect()->route('admin.research-studies.index')->with('status', 'Research study updated.');
    }

    public function destroy(ResearchStudy $study)
    {
        $path = $study->image_path;
        $study->delete();
        File::delete(public_path($path));

        return redirect()->route('admin.research-studies.index')->with('status', 'Research study deleted.');
    }

    private function saveStudy(Request $request, ResearchStudy $study): void
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'application_url' => ['required', 'url:http,https', 'max:2048'],
            'application_cutoff_at' => ['nullable', 'date'],
            'image' => [$study->image_path ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240', 'dimensions:width=500,height=500'],
        ]);

        $data['application_cutoff_at'] = $data['application_cutoff_at']
            ?? Carbon::parse($data['starts_at'])->subHours(24);
        unset($data['image']);
        $oldPath = $study->image_path;
        $newPath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = 'research-study-'.Str::uuid().'.'.$image->extension();
            File::ensureDirectoryExists(public_path('images/uploads'));
            $image->move(public_path('images/uploads'), $name);
            $data['image_path'] = $newPath = 'images/uploads/'.$name;
        }

        try {
            $study->fill($data)->save();
        } catch (\Throwable $exception) {
            if ($newPath) {
                File::delete(public_path($newPath));
            }
            throw $exception;
        }

        if ($newPath && $oldPath) {
            File::delete(public_path($oldPath));
        }
    }
}
