<?php

namespace Modulus\Framework\Mocks;

use Modulus\Utility\View;
use Modulus\Http\{Rest, Status};

trait FailedRoute
{
  /**
   * Handle event
   *
   * @return void
   */
  protected function handle(bool $isAjax, int $statuscode)
  {
    if ($statuscode == 404) {
      $title   = "Not found";
      $message = "Page not found";
    } else {
      $title   = "Method not allowed";
      $message = "Bad request";
    }

    if ($isAjax) {
      Rest::response($message, $code);
      exit;
    }

    View::make('app.errors.default', compact('title', 'message'));
    Status::set($statuscode);
    exit;
  }
}