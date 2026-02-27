<?php

namespace App\Modules\Authorization\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Authorization\Actions\CreatePermissionAction;
use App\Modules\Authorization\Actions\ListPermissionsAction;
use App\Modules\Authorization\Dtos\CreatePermissionDto;
use App\Modules\Authorization\Requests\CreatePermissionRequest;
use App\Modules\Authorization\Resources\PermissionResource;

class PermissionsController extends Controller
{
    public function index(ListPermissionsAction $action)
    {
        $permissions = $action->execute();
        return PermissionResource::collection($permissions);
    }
    public function store(CreatePermissionRequest $request,CreatePermissionAction $action) 
    {
        $dto = CreatePermissionDto::from($request->validated());

        $permission = $action->execute($dto);

        return response()->json([
            'success' => true,
            'message' => 'Permission created successfully.',
            'data' => new PermissionResource($permission),
        ], 201);
    }

}