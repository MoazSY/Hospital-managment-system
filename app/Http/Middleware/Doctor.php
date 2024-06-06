<?php

namespace App\Http\Middleware;

use App\Models\Doctor as ModelsDoctor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class Doctor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token=$request->bearerToken();
        if(!$token || !Auth::guard('doctor')->check()){
            return Response()->json(['error'=>'Unauthorized1'],401);
        }
        $user=Auth::guard('doctor')->user();
        if(ModelsDoctor::where('id','=',$user->id)){
        return $next($request);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }
}
