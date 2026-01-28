<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Membership;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * ログインユーザーが自分の会社でadminロールを持っているか確認
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->company_id) {
            abort(403, '会社に所属していません');
        }

        $membership = Membership::where('user_id', $user->id)
            ->where('company_id', $user->company_id)
            ->where('status', 'active')
            ->first();

        if (!$membership || $membership->role !== 'admin') {
            abort(403, '管理者権限がありません');
        }

        return $next($request);
    }
}
