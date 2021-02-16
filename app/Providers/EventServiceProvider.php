<?php

namespace App\Providers;

<<<<<<< HEAD
use App\Models\Appointment;
use App\Observers\AppointmentObserver;
=======
use App\Models\Doctor;
use App\Observers\DoctorObserver;
>>>>>>> ca10f98f9482a6b70878fba5aabd93d84c517313
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
<<<<<<< HEAD
        Appointment::observe(AppointmentObserver::class);
=======
        Doctor::observe(DoctorObserver::class);
>>>>>>> ca10f98f9482a6b70878fba5aabd93d84c517313
    }
}
