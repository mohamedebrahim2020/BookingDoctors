<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Jobs\StoreAppointment;
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

    public function approve($id)
    {
        $appointment = $this->repository->find($id);
        $doctor = request()->user();
        $this->checkDoctorHasThisAppointment($doctor, $appointment);
        $this->checkAvailabiltyToApprove($appointment);
        $this->update(['status' => AppointmentStatus::APPROVED] , $appointment->id);
        $appointment->patient->notify(new AppointmentNotification($this->repository->find($id)));
        return $appointment;
    }

    public function checkDoctorHasThisAppointment($doctor, $appointment)
    {
        $doctor->appointments->contains($appointment) ? "" : abort(Response::HTTP_BAD_REQUEST);
    }

    public function checkAvailabiltyToApprove($appointment)
    {
        $nowInMs = Carbon::now()->timestamp;
        if ($appointment->status == AppointmentStatus::APPROVED || $appointment->time <= $nowInMs) {
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
        StoreAppointment::dispatch($doctor->id, $appointment->id)->afterResponse();
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

    public function cancel($data, $appointmentId)
    {
        $data['status'] = AppointmentStatus::CANCELLED;
        $appointment = $this->repository->find($appointmentId);
        $doctor = auth()->user();  
        $this->checkDoctorHasThisAppointment($doctor, $appointment);
        $this->checkAvailabiltyToCancel($appointment);
        $this->update($data , $appointment->id);
        $appointment->patient->notify(new AppointmentNotification($this->repository->find($appointmentId)));
        return $appointment; 
    }

    public function checkAvailabiltyToCancel($appointment)
    {
        $time = Carbon::parse($appointment->time);
        $nowInMs = Carbon::now()->timestamp ;
        if ($appointment->status != AppointmentStatus::APPROVED || $appointment->time <= $nowInMs || $time->diffInHours() <= 24) {
            abort(Response::HTTP_BAD_REQUEST,"already cancelled or before less than 24 hrs or expired");            
        }
    }
        
    public function reject($data, $appointmentId)
    {
        $data['status'] = AppointmentStatus::REJECTED;
        $appointment = $this->repository->find($appointmentId);
        $doctor = auth()->user();  
        $this->checkDoctorHasThisAppointment($doctor, $appointment);
        $this->checkAvailabiltyToReject($appointment);
        $this->update($data , $appointment->id);
        $appointment->patient->notify(new AppointmentNotification($this->repository->find($appointmentId)));
        return $appointment;
    }

    public function checkAvailabiltyToReject($appointment)
    {
        $nowInMs = Carbon::now()->timestamp;
        if ($appointment->status != AppointmentStatus::PENDING || $appointment->time <= $nowInMs) {
            abort(Response::HTTP_BAD_REQUEST,"already rejected or expired");            
        }
    }

    public function complete($appointment)
    {
        $nowInMs = Carbon::now()->timestamp;
        $appointmentFinishedAt = ($appointment->time + ($appointment->duration * 60));
        if ($appointmentFinishedAt > $nowInMs || $appointment->status != AppointmentStatus::CHECKED) {
            abort(Response::HTTP_BAD_REQUEST,"not checked or not finished or already completed");            
        }
        $this->update(['status' => AppointmentStatus::COMPLETED] , $appointment->id);
        $appointment->patient->notify(new AppointmentNotification($this->show($appointment->id)));
    }
}    
