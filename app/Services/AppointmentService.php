<?php

namespace App\Services;

use App\Repositories\AppointmentRepository;
use Carbon\Carbon;
use Illuminate\Http\Response;

class AppointmentService extends BaseService
{
    public function __construct(AppointmentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function storeAppointment($data, $doctorID)
    {
        $doctor = app(DoctorService::class)->checkDoctorIsActivated($doctorID);
        $shift = app(DoctorService::class)->repository->fiterDoctorShifts($doctorID);
        $this->checkDurationWithShift($shift, $data['duration']);
        $approvedAppointments = $this->repository->fiterDoctorAppointments($doctor);
        $this->checkDurationWithAppointment($approvedAppointments, $data);
        $appointment = $this->repository->storeAppointment($data, $doctor);
        app(DoctorService::class)->recieveAppointmentRequest($doctorID,$data, auth()->user()->name);
        return $appointment;
    }

    public function checkDurationWithShift($shift, $duration)
    {
        if ($shift->count() == 0) {
            abort(Response::HTTP_BAD_REQUEST, 'doctor has no shift at this time');
        } else {
            $shift = $shift->toArray();
            if ($shift[0]['is_all_day'] == 1) {
                return true;
            } else {
                $shiftDuration = Carbon::parse($shift[0]["to"])->diffInMinutes(Carbon::parse($shift[0]["from"]));
                ($shiftDuration < $duration) ? abort(Response::HTTP_BAD_REQUEST, 'duration is longer than shift'):'';
            }
        } 
    }

    public function checkDurationWithAppointment($approvedAppointments, $data)
    {
        if ($approvedAppointments->count() > 0) {
            $appointments = $approvedAppointments->map(
                function ($item, $key) use ($data) {
                    return (($item->time + ($item->duration * 60000)) > $data['time']);
                }
            );
            ($appointments[0]) ? abort(Response::HTTP_BAD_REQUEST, "there is an already approved appointment at this time") : "";
        }
    }
}    