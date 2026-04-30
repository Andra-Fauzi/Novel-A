<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthor
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!auth()->check()) {
            return redirect('/login');
        }

        if(auth()->user()->role !== 'author') {
            abort(403, "AKSES DITOLAK");
        }
        return $next($request);
    }
}
