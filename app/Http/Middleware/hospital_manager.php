<?php

namespace App\Http\Middleware;

use App\Models\Hospital_manager as Hospital;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class hospital_manager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token=$request->bearerToken();
        if ( !$token||!Auth::guard('hospital_manager')->check()) {
            return response()->json(['error' => 'Unauthorized1'], 401);
        }

        $user = Auth::guard('hospital_manager')->user();
      if(Hospital::where('id','=',$user->id)){
        return $next($request);

      }
      return response()->json(['error' => 'Unauthorized'], 401);

    }
}
