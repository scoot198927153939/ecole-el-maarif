<?php

namespace App\Http\Middleware;

use App\Support\TeachingModules;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdminOrTeacher
{
    /**
     * يسمح بالوصول للأدمن والأستاذ دائماً، ولأي مستخدم آخر (كالمشرف) فقط
     * إن مُنح صلاحية وحدة التدريس المطلوبة تحديداً.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة.');
        }

        if (in_array($user->role, ['admin', 'teacher'], true)) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        $key = $routeName ? TeachingModules::permissionKeyForRoute($routeName) : null;

        if ($key && $user->hasAdminPermission($key)) {
            return $next($request);
        }

        abort(403, 'غير مصرح لك بالوصول لهذه الصفحة.');
    }
}
