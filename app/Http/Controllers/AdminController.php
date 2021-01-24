<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddAdminRequest;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Admin;
use App\Services\AdminService;
use App\Traits\LoginTrait;
use App\Services\DoctorService;
use App\Transformers\AdminResource;
use App\Transformers\CreatedAdminResource;
use App\Transformers\PermissionResource;
use App\Transformers\TokenResource;
use Illuminate\Http\Response;

class AdminController extends Controller
{
    use LoginTrait;

    protected $adminService;
    protected $doctorService;

    public function __construct(AdminService $adminService, DoctorService $doctorService)
    {
        $this->adminService = $adminService;
        $this->doctorService = $doctorService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function login(AdminLoginRequest $request)
    {
        $this->adminService->checkAuth($request->all());
        return response()->json(new TokenResource($this->requestTokensFromPassport($request)), Response::HTTP_OK);
        
    } 

    public function index()
    {
        return response()->json(AdminResource::collection($this->adminService->index()), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddAdminRequest $request)
    {
        $this->authorize('create', Admin::class);
        $admin = $this->adminService->store($request->all());
        return response()->json(new CreatedAdminResource($admin), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(new AdminResource($this->adminService->show($id)), Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAdminRequest $request, $id)
    {

        $this->authorize('update', $this->adminService->show($id));
        $this->adminService->updateAdmin($request, $id);
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete', $this->adminService->show($id));
        $this->adminService->delete($this->adminService->show($id));
        return response()->json([], Response::HTTP_OK);
    }

    public function getPermissions()
    {
        return response()->json(PermissionResource::collection($this->adminService->permissions()), Response::HTTP_OK); 
    }

    public function activateDoctor($doctor)
    {
        $this->authorize('activateDoctor', Admin::class);
        $this->doctorService->activateDoctor($doctor);
        return response()->json([], Response::HTTP_OK);
    }
}
