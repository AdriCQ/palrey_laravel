<?php

namespace App\Http\Middleware\Olympus;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
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
    if (auth()->check() && auth()->user()->hasAnyRole(['Admin', 'Developer']))
      return $next($request);
    return response()->json([
      'STATUS' => false,
      'ERRORS' => ['No está autorizado. Necesita ser Vendedor para acceder'],
      'DATA' => null
    ], 401);
  }
}
