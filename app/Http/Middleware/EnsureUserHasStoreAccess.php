<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasStoreAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $storeId = $request->route('store');
        
        if (!$storeId || !$request->user()->hasStoreAccess($storeId)) {
            abort(403, 'You do not have access to this store.');
        }

        return $next($request);
    }
}
