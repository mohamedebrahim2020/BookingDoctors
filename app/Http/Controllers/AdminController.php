<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Admin;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $this->adminService->store($request);
        return response()->json(null, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAdminRequest $request, $id)
    {

        $this->authorize('update', Admin::findorfail($id));
        $this->adminService->updateAdmin($request, $id);
        return response()->json(null, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $admin)
    {
        if ($this->authorize('delete', $admin)) {
            $this->adminService->delete($admin);
            return response()->json(null, Response::HTTP_OK);
        }
    }

    public function getPermissions()
    {
        return response()->json(PermissionResource::collection($this->adminService->permissions()), Response::HTTP_OK); 
    }
}
