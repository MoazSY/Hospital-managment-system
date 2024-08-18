<?php

namespace App\Http\Middleware;

use App\Models\Consumer_employee;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Consumer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token=$request->bearerToken();
        if(!$token || !Auth::guard('consumer_employee')->check()){
            return Response()->json(['error'=>'Unauthorized1'],401);
        }
        $user=Auth::guard('consumer_employee')->user();
        if(Consumer_employee::where('id','=',$user->id)){
        return $next($request);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
