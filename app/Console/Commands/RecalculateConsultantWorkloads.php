<?php
// app/Console/Commands/RecalculateConsultantWorkloads.php

namespace App\Console\Commands;

use App\Models\Consultant;
use Illuminate\Console\Command;

class RecalculateConsultantWorkloads extends Command
{
    protected $signature = 'consultants:recalculate-workloads';
    protected $description = 'Recalculate workload metrics for all consultants';

    public function handle()
    {
        $this->info('Recalculating consultant workloads...');
        
        $consultants = Consultant::all();
        $bar = $this->output->createProgressBar($consultants->count());

        foreach ($consultants as $consultant) {
            $consultant->updateWorkloadMetrics();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Workload metrics recalculated successfully!');
    }
}