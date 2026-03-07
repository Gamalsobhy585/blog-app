<?php

use Illuminate\Support\Facades\Route;



require __DIR__ . '/../app/Modules/Author/Routes/author.php';
require __DIR__ . '/../app/Modules/Book/Routes/book.php';
require __DIR__ . '/../app/Modules/Notification/Routes/notifications.php';
require __DIR__ . '/../app/Modules/User/adminRoutes.php';
require __DIR__ . '/../app/Modules/Authorization/Routes/authorization.php';
require __DIR__ . '/../app/Modules/Auth/authRoutes.php';





Route::middleware('auth:sanctum')->get('/debug-auth', function () {
    $user = request()->user();
    return response()->json([
        'authenticated'  => (bool) $user,
        'user_id'        => $user?->id,
        'user_name'      => $user?->name,
        'user_email'     => $user?->email,
        'roles'          => $user?->getRoleNames(),
        'permissions'    => $user?->getAllPermissions()->pluck('name'),
        'token_user'     => auth('sanctum')->user()?->id,
    ]);
});