<?php

namespace App\Http\Middleware;

use App\Models\Laboratory as ModelsLaboratory;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Laboratory
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token=$request->bearerToken();
        if ( !$token||!Auth::guard('laboratory')->check()) {
            return response()->json(['error' => 'Unauthorized1'], 401);
        }

        $user = Auth::guard('laboratory')->user();
      if(ModelsLaboratory::where('id','=',$user->id)){
        return $next($request);
      }
      return response()->json(['error' => 'Unauthorized'], 401);
    }
}
