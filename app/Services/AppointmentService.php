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

} 