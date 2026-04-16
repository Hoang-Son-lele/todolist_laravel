<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:scan-task-deadlines')]
#[Description('Command description')]
class ScanTaskDeadlines extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {

        $today = now()->toDateString();
        $tasks = \App\Models\Task::whereDate('end_date', '<=', $today)
            ->where('status', '!=', 'completed')
            ->get();

        if ($tasks->isEmpty()) {
            $this->info("Không tìm thấy task nào hết hạn.");
            return;
        }

        foreach ($tasks as $task) {

            if ($task->assignedTo) {
                $task->assignedTo->notify(new \App\Notifications\TaskDeadlineWarning($task));
            }
        }

        $this->info('Đã gửi thông báo hết hạn cho ' . $tasks->count() . ' task.');
    }
}
