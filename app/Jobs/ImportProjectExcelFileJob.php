<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Imports\ProjectDynamicImport;
use App\Imports\ProjectImport;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ImportProjectExcelFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $path;
    private Task $task;

    public function __construct(string $path, Task $task)
    {
        $this->path = $path;
        $this->task = $task;
    }

    public function handle()
    : void
    {
        $this->task->update([
            'status' => Task::STATUS_SUCCESS,
        ]);
        $methodName = 'importOfType' . $this->task->type;
        $this->$methodName();
    }

    public function importOfType1()
    : void
    {
        Excel::import(new ProjectImport($this->task), $this->path, 'public');
    }

    public function importOfType2()
    : void
    {
        Excel::import(new ProjectDynamicImport($this->task), $this->path, 'public');
    }

}
