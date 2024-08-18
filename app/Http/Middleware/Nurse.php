<?php

namespace App\Http\Middleware;

use App\Models\Nurse as ModelsNurse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Nurse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token=$request->bearerToken();
        if ( !$token||!Auth::guard('nurse')->check()) {
            return response()->json(['error' => 'Unauthorized1'], 401);
        }

        $user = Auth::guard('nurse')->user();
      if(ModelsNurse::where('id','=',$user->id)){
        return $next($request);
      }
      return response()->json(['error' => 'Unauthorized'], 401);
    }
}
