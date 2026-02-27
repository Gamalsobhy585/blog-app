<?php

namespace App\Modules\Authorization\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Authorization\Actions\CreateRoleAction;
use App\Modules\Authorization\Actions\ListRolesAction;
use App\Modules\Authorization\Dtos\CreateRoleDto;
use App\Modules\Authorization\Requests\CreateRoleRequest;
use App\Modules\Authorization\Resources\RoleResource;

class RolesController extends Controller
{
    public function index(ListRolesAction $action)
    {
        $roles = $action->execute();
        return RoleResource::collection($roles);
    }


    public function store(CreateRoleRequest $request,CreateRoleAction $action) 
    {
        $dto = CreateRoleDto::from($request->validated());

        $role = $action->execute($dto);

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully.',
            'data' => new RoleResource($role),
        ], 201);
    }

}