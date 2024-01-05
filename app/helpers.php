<?php

declare(strict_types=1);

use App\Models\FailedRow;

if (!function_exists('processFailures')) {
    function processFailures($failures, $attributesMap, $task)
    : void {
        $map = [];
        foreach ($failures as $failure) {
            foreach ($failure->errors() as $error) {
                $map[] = [
                    'key'     => $attributesMap[$failure->attribute()],
                    'row'     => $failure->row(),
                    'message' => $error,
                    'task_id' => $task->id,
                ];
            }
        }
        if (count($map) > 0) {
            FailedRow::insertFailedRows($map, $task);
        }
    }
}
