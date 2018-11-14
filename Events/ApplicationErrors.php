<?php

namespace Modulus\Framework\Events;

use Modulus\Http\Rest;
use Modulus\Utility\View;
use Modulus\Utility\Event;

class ApplicationErrors extends Event
{
  /**
   * Handle event
   *
   * @return void
   */
  protected function handle($exception)
  {
    if ($this->isAjax()) {
      $this->toJson($exception);
      return;
    }

    $this->toHtml($exception);
  }

  /**
   * Render json
   *
   * @param mixed $exception
   * @return void
   */
  private function toJson($exception)
  {
    Rest::response()->json([
      'status' => 'This resource isn’t working',
      'code'   => 500
    ], 500);
  }

  /**
   * Render html
   *
   * @param mixed $exception
   * @return void
   */
  private function toHtml($exception)
  {
    View::make('app.errors.default', [
      'title'      => 'This page isn’t working',
      'message'    => 'Oops, something happened.',
      'statusCode' => 500
    ]);
  }

  /**
   * Check if request is isAjax or not
   *
   * @return bool
   */
  private function isAjax() : bool
  {
    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                            ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
  }
}
