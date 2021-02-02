<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestAppointmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $doctor;
    public $appointment;
    public $patient;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($doctor, $appointment, $patient)
    {
        $this->doctor = $doctor;
        $this->appointment = $appointment;
        $this->patient = $patient;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->markdown(
            'mail.requestAppointmentMail',
            [
                'doctor' => $this->doctor,
                'time' => Carbon::createFromTimestamp($this->appointment['time']) ->toDateTimeString(), 
                'duration' => $this->appointment['duration'],
                'patient' => $this->patient
            ]
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
