<?php

namespace App\Http\Middleware;

use App\Support\AdminModules;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminAccess
{
    /**
     * يسمح بالوصول للأدمن دائماً، ولأي مستخدم آخر لديه صلاحية الوحدة المطلوبة تحديداً.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة.');
        }

        if ($user->role === 'admin') {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        $key = $routeName ? AdminModules::permissionKeyForRoute($routeName) : null;

        if ($key && $user->hasAdminPermission($key)) {
            return $next($request);
        }

        abort(403, 'غير مصرح لك بالوصول لهذه الصفحة.');
    }
}
