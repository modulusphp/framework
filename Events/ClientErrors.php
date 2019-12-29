<?php

namespace Modulus\Framework\Events;

use Modulus\Utility\View;
use Modulus\Utility\Event;
use Modulus\Http\{Rest, Status};
use Modulus\Framework\Exceptions\ClientErrorsException;

class ClientErrors extends Event
{
  /**
   * Handle event
   *
   * @param ClientErrorsException $exception
   * @return void
   */
  protected function handle(ClientErrorsException $exception)
  {
    if (
      $exception->getStatusCode() == 403 &&
      method_exists($this, 'renderAccessDenied')
    ) {
      return $this->renderAccessDenied($exception);
    } else if (
      $exception->getStatusCode() == 404 &&
      method_exists($this, 'renderNotFound')
    ) {
      return $this->renderNotFound($exception);
    } else if (
      $exception->getStatusCode() == 405 &&
      method_exists($this, 'renderNotAllowed')
    ) {
      return $this->renderAccessDenied($exception);
    } else if (
      $exception->getStatusCode() == 429 &&
      method_exists($this, 'renderTooManyRequests')
    ) {
      return $this->renderTooManyRequests($exception);
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
   * @param ClientErrorsException $exception
   * @return void
   */
  private function toJson(ClientErrorsException $exception)
  {
    Rest::response()->json([
      'status' => $exception->getMessage(),
      'code'   => $exception->getStatusCode()
    ], $exception->getStatusCode());
  }

  /**
   * Render html
   *
   * @param ClientErrorsException $exception
   * @return void
   */
  private function toHtml(ClientErrorsException $exception)
  {
    View::make('app.errors.default', [
      'title'      => $exception->getTitle(),
      'message'    => $exception->getMessage(),
      'statusCode' => $exception->getStatusCode()
    ]);
  }
}
