<?php

namespace App\Services;

use App\Enums\FolderName;
use App\Models\Doctor;
use App\Repositories\DoctorRepository;
use Illuminate\Support\Facades\Hash;
use App\Notifications\DoctorActivationMail;
use App\Notifications\RequestAppointmentNotification;
use App\Repositories\ReviewRepository;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class ReviewService extends BaseService
{
    public function __construct(ReviewRepository $repository)
    {
        $this->repository = $repository;
    }
}    