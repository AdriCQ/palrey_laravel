<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  protected $API_RESPONSE = ['STATUS' => false, 'DATA' => null, 'ERRORS' => null];

  protected $API_STATUS = 200;

  /**
   * 
   */
  protected $AVAILABLE_STATUS = [
    // Succefull
    'OK' => 200,
    'CREATED' => 201,
    'NO_CONTENT' => 204,
    // Client Error
    'BAD_REQUEST' => 400,
    'UNAUTHORIZED' => 401,
    // Server Error
    'NOT_IMPLEMENTED' => 501,
    'SERVICE_UNAVAILABLE' => 503
  ];
}
