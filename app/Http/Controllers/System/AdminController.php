<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminFormRequest;
use App\Http\Requests\Admin\UpdateAdminFormRequest;
use App\Http\Resources\Admin\AdminsResource;
use App\Models\Admin;
use App\Models\ProviderShopDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $admins = Admin::get();
        return $this->paginateCollection(AdminsResource::collection($admins), $request->limit, 'admins');
    }



    public function show($id)
    {
        $admin = Admin::findOrFail($id);
        return $this->dataResponse(['admin' => new AdminsResource($admin)], 'success', 200);
    }


    public function deactivateAdminAccount($id)
    {
        $superAdmin = Auth::user();
        if ($superAdmin->type == 'super_admin') {
            $admin = Admin::findOrFail($id);
            $admin->update(['status' => 'deactivated']);
            return $this->successResponse('success', 200);
        }
        return $this->errorResponseWithMessage('Unauthorized', 401);
    }
    public function activateAdminAccount($id)
    {
        $superAdmin = Auth::user();
        if ($superAdmin->type == 'super_admin') {
            $admin = Admin::findOrFail($id);
            $admin->update(['status' => 'active']);
            return $this->successResponse('success', 200);
        }
        return $this->errorResponseWithMessage('Unauthorized', 401);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editMyAccount(UpdateAdminFormRequest $request)
    {
        $admin = Auth::user();
        $admin->update($request->input());
        return $this->dataResponse(['admin' => new AdminsResource($admin)], 'success', 200);
    }

    public function addOperation(AdminFormRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        $operation = Admin::create($data);
        return $this->dataResponse(['operation' => new AdminsResource($operation)], 'success', 201);
    }
}
