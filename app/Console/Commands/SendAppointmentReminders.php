<?php
// app/Console/Commands/SendAppointmentReminders.php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Mail\AppointmentReminder24Hours;
use App\Mail\AppointmentReminder1Hour;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Send appointment reminders';

    public function handle()
    {
        Log::info('Appointment reminders started at ' . now());

        try {
            $this->send24HourReminders();
            $this->send1HourReminders();
            
            Log::info('Appointment reminders completed successfully');
            return 0;
        } catch (\Exception $e) {
            Log::error('Appointment reminders failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function send24HourReminders(): void
    {
        $targetTime = now()->addHours(24);

        $appointments = Appointment::where('status', 'scheduled')
            ->whereBetween('scheduled_at', [
                $targetTime->copy()->subMinutes(5),
                $targetTime->copy()->addMinutes(5)
            ])
            ->where(function($query) {
                $query->whereNull('reminder_settings->24_hour_sent')
                      ->orWhere('reminder_settings->24_hour_sent', false);
            })
            ->get();

        foreach ($appointments as $appointment) {
            try {
                Mail::to($appointment->applicant->email)
                    ->send(new AppointmentReminder24Hours($appointment, 'applicant'));

                Mail::to($appointment->consultant->email)
                    ->send(new AppointmentReminder24Hours($appointment, 'consultant'));

                $reminderSettings = $appointment->reminder_settings ?? [];
                $reminderSettings['24_hour_sent'] = true;
                $reminderSettings['24_hour_sent_at'] = now()->toDateTimeString();

                $appointment->update([
                    'reminder_settings' => $reminderSettings,
                    'last_reminder_sent' => now(),
                ]);

                Log::info("24-hour reminder sent for appointment #{$appointment->id}");
            } catch (\Exception $e) {
                Log::error("Failed to send 24-hour reminder for appointment #{$appointment->id}: " . $e->getMessage());
            }
        }
    }

    private function send1HourReminders(): void
    {
        $targetTime = now()->addHour();

        $appointments = Appointment::where('status', 'scheduled')
            ->whereBetween('scheduled_at', [
                $targetTime->copy()->subMinutes(5),
                $targetTime->copy()->addMinutes(5)
            ])
            ->where(function($query) {
                $query->whereNull('reminder_settings->1_hour_sent')
                      ->orWhere('reminder_settings->1_hour_sent', false);
            })
            ->get();

        foreach ($appointments as $appointment) {
            try {
                Mail::to($appointment->applicant->email)
                    ->send(new AppointmentReminder1Hour($appointment, 'applicant'));

                Mail::to($appointment->consultant->email)
                    ->send(new AppointmentReminder1Hour($appointment, 'consultant'));

                $reminderSettings = $appointment->reminder_settings ?? [];
                $reminderSettings['1_hour_sent'] = true;
                $reminderSettings['1_hour_sent_at'] = now()->toDateTimeString();

                $appointment->update([
                    'reminder_settings' => $reminderSettings,
                    'last_reminder_sent' => now(),
                ]);

                Log::info("1-hour reminder sent for appointment #{$appointment->id}");
            } catch (\Exception $e) {
                Log::error("Failed to send 1-hour reminder for appointment #{$appointment->id}: " . $e->getMessage());
            }
        }
    }
}