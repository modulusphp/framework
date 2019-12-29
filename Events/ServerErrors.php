<?php

namespace Modulus\Framework\Events;

use Modulus\Http\Rest;
use Modulus\Utility\View;
use Modulus\Utility\Event;
use Modulus\Framework\Exceptions\ServerErrorsException;

class ServerErrors extends Event
{
  /**
   * Handle event
   *
   * @param ServerErrorsException $exception
   * @return void
   */
  protected function handle(ServerErrorsException $exception)
  {
    if (
      $exception->getStatusCode() == 503 &&
      method_exists($this, 'renderServiceUnavailable')
    ) {
      return $this->renderServiceUnavailable($exception);
    }

    if ($exception->isAjax()) {
      $this->toJson($exception);
      return;
    }

    $this->toHtml($exception);
  }

  /**
   * Render json
   *
   * @param ServerErrorsException $exception
   * @return void
   */
  private function toJson(ServerErrorsException $exception)
  {
    Rest::response()->json([
      'status' => $exception->getMessage(),
      'code'   => $exception->getStatusCode()
    ], $exception->getStatusCode());
  }

  /**
   * Render html
   *
   * @param ServerErrorsException $exception
   * @return void
   */
  private function toHtml(ServerErrorsException $exception)
  {
    View::make('app.errors.default', [
      'title'      => $exception->getTitle(),
      'message'    => $exception->getMessage(),
      'statusCode' => $exception->getStatusCode()
    ]);
  }
}
