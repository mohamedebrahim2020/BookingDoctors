<?php

namespace App\Console\Commands;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Notifications\SendDailyAppointmentNotification;
use App\Services\AppointmentService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointmentReminder:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'notify patient with today approved appointment';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        app(AppointmentService::class)->notifyPatientWithDailyAppointment();
    }
}
