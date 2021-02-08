<?php

namespace App\Jobs;

use App\Services\FirebaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreAppointment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $doctorId;
    public $appointmentId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($doctorId, $appointmentId)
    {
        $this->doctorId = $doctorId;
        $this->appointmentId = $appointmentId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app(FirebaseService::class)->setAppointment($this->doctorId, $this->appointmentId);
    }
}
