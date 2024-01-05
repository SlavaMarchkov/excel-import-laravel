<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Imports\ProjectDynamicImport;
use App\Imports\ProjectImport;
use App\Models\Task;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ExcelCommand extends Command
{
    protected $signature = 'excel';

    protected $description = 'Process an Excel file';

    public function handle()
    : void
    {
//        Excel::import(new ProjectImport(), 'files/projects.xlsx', 'public');
        Excel::import(new ProjectDynamicImport(Task::find(4)), 'files/projects2.xlsx', 'public');
//        Excel::import(new ProjectImport(), 'files/projects_with_headings.xlsx', 'public');
    }

}
