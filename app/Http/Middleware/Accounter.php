<?php

namespace App\Http\Middleware;

use App\Models\Accounter as ModelsAccounter;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Accounter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $token=$request->bearerToken();
        if(!$token || !Auth::guard('accounter')->check()){
            return Response()->json(['error'=>'Unauthorized1'],401);
        }
        $user=Auth::guard('accounter')->user();
        if(ModelsAccounter::where('id','=',$user->id)){
        return $next($request);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
