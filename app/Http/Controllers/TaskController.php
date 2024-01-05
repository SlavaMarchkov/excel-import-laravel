<?php

namespace App\Http\Controllers;

use App\Http\Resources\FailedRow\FailedRowResource;
use App\Http\Resources\Task\TaskResource;
use App\Models\FailedRow;
use App\Models\Task;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['user', 'file'])->withCount('failedRows')->paginate(3);
        $tasks = TaskResource::collection($tasks);
        return Inertia::render( 'Task/Index', compact('tasks') );
    }

    public function failedList(Task $task)
    {
        $failedRows = FailedRow::where('task_id', $task->id)->paginate(5);
        $failedList = FailedRowResource::collection($failedRows);
        return Inertia::render('Task/FailedList', compact('failedList'));
    }
}
