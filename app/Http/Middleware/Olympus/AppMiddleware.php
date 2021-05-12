<?php

namespace App\Http\Middleware\Olympus;

use App\Models\Olympus\App as OlympusApplication;
use Closure;
use Illuminate\Http\Request;

class AppMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    // return $next($request);
    if ($request->has('ol_app_token')) {
      if (OlympusApplication::checkToken($request['ol_app_token'])) {
        // $app = OlympusApplication::getByToken($request['ol_app_token'], ['visits']);
        // $app->visits++;
        // $app->save();
        return $next($request);
      }
    }
    return response()->json([
      'ERRORS' => ['Aplicacion no valida']
    ], 401);
  }
}
