<?php

namespace App\Console\Commands;

use App\Models\Application;
use Illuminate\Console\Command;

class RecalculateApplicationProgress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'applications:recalculate-progress {--application_id= : Specific application ID to recalculate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate completion percentage for all applications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting completion percentage recalculation...');

        $query = Application::query();

        // If specific application ID provided
        if ($applicationId = $this->option('application_id')) {
            $query->where('id', $applicationId);
        }

        $applications = $query->get();
        $total = $applications->count();

        if ($total === 0) {
            $this->warn('No applications found to recalculate.');
            return 0;
        }

        $this->info("Found {$total} application(s) to recalculate.");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $updated = 0;
        $errors = 0;

        foreach ($applications as $application) {
            try {
                $oldPercentage = $application->completion_percentage;
                $application->updateCompletionPercentage();
                $newPercentage = $application->completion_percentage;

                if ($oldPercentage !== $newPercentage) {
                    $updated++;
                    $this->newLine();
                    $this->info("✅ Application #{$application->application_number}: {$oldPercentage}% → {$newPercentage}%");
                }

                $bar->advance();
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("❌ Error for Application #{$application->application_number}: {$e->getMessage()}");
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✨ Recalculation completed!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Processed', $total],
                ['Updated', $updated],
                ['Unchanged', $total - $updated - $errors],
                ['Errors', $errors],
            ]
        );

        return 0;
    }
}