<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Reseption_employee as ControllersReseption_employee;
use App\Models\Reseption_employee as ModelsReseption_employee;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
class reseption_employee
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token=$request->bearerToken();
        if ( !$token||!Auth::guard('reseption_employee')->check()) {
            return response()->json(['error' => 'Unauthorized1'], 401);
        }

        $user = Auth::guard('reseption_employee')->user();
      if(ModelsReseption_employee::where('id','=',$user->id)){
        return $next($request);
      }
      return response()->json(['error' => 'Unauthorized'], 401);
    }
}

