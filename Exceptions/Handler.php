<?php

namespace Modulus\Framework\Exceptions;

use Exception;
use Modulus\Utility\Events;
use Modulus\Http\Exceptions\NotFoundHttpException;
use Modulus\Framework\Exceptions\ClientErrorsException;
use Modulus\Framework\Exceptions\ServerErrorsException;
use Modulus\Http\Exceptions\ServiceUnavailableHttpException;

class Handler
{
  /**
   * Render errors
   *
   * @param \Modulus\Http\Request
   * @param Exception $exception
   * @return
   */
  public function render($request, Exception $exception)
  {
    $exception = $this->prepare($exception);

    if ($exception instanceof ClientErrorsException) {
      return $exception->handle();
    }

    if ($exception instanceof ServerErrorsException) {
      return $exception->handle();
    }

    Events::trigger('internal.error', [$exception]);
  }

  /**
   * Prepare and convert exceptions
   *
   * @param Exception $exception
   * @return Exception $exception
   */
  public function prepare(Exception $exception)
  {
    if ($exception instanceof NotFoundHttpException) {
      return $exception->createsClientError();
    } else if ($exception instanceof ServiceUnavailableHttpException) {
      return $exception->createsServerError();
    }

    return $exception;
  }
}
