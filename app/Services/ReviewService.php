<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Repositories\ReviewRepository;
use Illuminate\Http\Response;

class ReviewService extends BaseService
{
    public function __construct(ReviewRepository $repository)
    {
        $this->repository = $repository;
    }

    public function store($data)
    {
        $appointment = $this->checkAppointmentExistence(request()->appointment_id);
        $this->checkPatienHasThisAppointment($appointment);
        $this->checkAppointmentHasNoReview($appointment);
        $this->checkAppointmentIsCompleted($appointment);
        $review = $this->repository->storeReview($data, $appointment);
        return $review;
    }

    public function checkPatienHasThisAppointment($appointment)
    {
        if ($appointment->patient->id != auth()->user()->id) {
            abort(Response::HTTP_FORBIDDEN);
        }
        
    }

    public function checkAppointmentExistence($id)
    {
        $appointment = app(AppointmentService::class)->show($id);
        return $appointment;
    }

    public function checkAppointmentHasNoReview($appointment)
    {
        if ($appointment->review) {
            abort(Response::HTTP_BAD_REQUEST, 'this appointment already has review');
        }
    }

    public function checkAppointmentIsCompleted($appointment)
    {
        if ($appointment->status != AppointmentStatus::COMPLETED) {
            abort(Response::HTTP_BAD_REQUEST, 'this appointment is not completed to review');
        }
    }

    public function respond($data, $id)
    {
        $review = $this->show($id);
        $this->checkDoctorHasReview($review);
        $this->checkReviewNotRespondedBefore($review);
        $this->update($data, $id);        
    }

    public function checkDoctorHasReview($review)
    {
        if ($review->appointment->doctor->id != auth('doctor')->user()->id) {
            abort(Response::HTTP_FORBIDDEN);
        }
    }

    public function checkReviewNotRespondedBefore($review)
    {
        if ($review->respond != null) {
            abort(Response::HTTP_BAD_REQUEST, 'this review was responded before');
        }
    }
}    