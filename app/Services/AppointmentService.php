<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Notifications\AppointmentNotification;
use App\Repositories\AppointmentRepository;
use Carbon\Carbon;
use Illuminate\Http\Response;

class AppointmentService extends BaseService
{
    public function __construct(AppointmentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        if (request()->user('doctor')) {
            return $this->repository->filterAppointmentsByStatus();
        }
    }

    public function approve()
    {
        $appointment = $this->repository->find(request()->appointment);
        $doctor = request()->user();
        $this->checkDoctorHasThisAppointment($doctor, $appointment);
        $this->checkAvailabiltyToApprove($appointment);
        $this->update(['status' => AppointmentStatus::APPROVED] , $appointment->id);
        $appointment->patient->notify(new AppointmentNotification($this->repository->find(request()->appointment)));
    }

    public function checkDoctorHasThisAppointment($doctor, $appointment)
    {
        $doctor->appointments->contains($appointment) ? "" : abort(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function checkAvailabiltyToApprove($appointment)
    {
        $nowInMs = Carbon::now()->timestamp * 1000;
        if ($appointment->status ==2 || $appointment->time <= $nowInMs) {
            abort(Response::HTTP_BAD_REQUEST,"already approved or expired");            
        }
    }

 
    public function store($data)
    {
        $doctor = app(DoctorService::class)->checkDoctorIsActivated(request()->doctor);
        $shift = app(DoctorService::class)->repository->fiterDoctorShifts(request()->doctor);
        $this->checkDurationWithShift($shift, $data['duration']);
        $approvedAppointments = $this->repository->fiterDoctorAppointments($doctor);
        $this->checkAppointment($approvedAppointments);
        $appointment = $this->repository->storeAppointment($data, $doctor);
        app(DoctorService::class)->recieveAppointmentRequest(request()->doctor, $data, auth()->user()->name);
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

    public function checkAppointment($approvedAppointments)
    {
        if ($approvedAppointments->count() > 0) {
            abort(Response::HTTP_BAD_REQUEST, "there is an already approved appointment at this time");
        }
    }
}    
