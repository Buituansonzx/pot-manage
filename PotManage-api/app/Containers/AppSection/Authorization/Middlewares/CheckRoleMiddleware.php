<?php

namespace App\Containers\AppSection\Authorization\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized. Please login first.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Nếu là admin (như Tuấn Sơn) thì auto pass mọi route.
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Tránh lỗi LazyLoadingViolationException khi truy cập $user->roles
        $user->loadMissing('roles');

        // Lấy danh sách các mã code (code role) của user hiện tại hiện có
        $userRoleCodes = $user->roles->pluck('code')->toArray();

        // Kiểm tra xem user có ít nhất 1 role khớp với các code được truyền vào middleware không
        $hasRole = !empty(array_intersect($roles, $userRoleCodes));

        if (!$hasRole) {
            return response()->json([
                'message' => 'Forbidden. You do not have the required role access.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
