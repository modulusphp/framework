<?php

namespace Modulus\Framework\Mocks;

use Modulus\Utility\View;
use Modulus\Http\{Status, Rest};
use AtlantisPHP\Telemonlog\Output;

trait InternalError
{
  /**
   * Handle event
   *
   * @return void
   */
  protected function handle($exception, $inspector, $isAjax, ?int $statuscode = 500)
  {
    Output::error($exception);

    if ($isAjax) {
      Rest::response()->code(500)->send();
      exit;
    }

    $title   = "This page isn’t working";
    $message = "Internal Server Error";

    if (str_contains($exception, 'Session has expired')) {
      $message = 'Session has expired';
    }

    View::make('app.errors.default', compact('title', 'message', 'statuscode'));
    Status::set($statuscode);
    exit;
  }
}