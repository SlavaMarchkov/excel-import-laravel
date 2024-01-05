<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\ImportStoreRequest;
use App\Http\Resources\Project\ProjectResource;
use App\Jobs\ImportProjectExcelFileJob;
use App\Models\File;
use App\Models\Project;
use App\Models\Task;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::paginate(5);
        $projects = ProjectResource::collection($projects);
        return Inertia::render('Project/Index', compact('projects'));
    }

    public function import()
    {
        return Inertia::render('Project/Import');
    }

    public function importStore(ImportStoreRequest $request)
    {
        $data = $request->validated();
        $file = File::putAndCreate($data['file']);
        $task = Task::create([
            'user_id' => auth()->id(),
            'file_id' => $file->id,
            'type'    => $data['type'],
        ]);

        ImportProjectExcelFileJob::dispatch($file->path, $task)->onQueue('imports');

        return redirect()->back()->with(['message' => 'Excel import in process']);
    }
}
