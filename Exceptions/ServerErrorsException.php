<?php

namespace Modulus\Framework\Exceptions;

use Exception;
use Modulus\Http\Status;
use Modulus\Support\Config;
use Modulus\Utility\Events;

class ServerErrorsException extends Exception
{
  /**
   * $isAjax
   *
   * @var boolean
   */
  protected $isAjax = false;

  /**
   * $statusCode
   *
   * @var integer
   */
  protected $statusCode = 503;

  /**
   * $title
   *
   * @var string
   */
  protected $title = 'Service Unavailable';

  /**
   * __construct
   *
   * @return void
   */
  public function __construct(string $message = "Service Unavailable", bool $isAjax = false, int $code = 503)
  {
    $this->isAjax     = $isAjax;
    $this->statusCode = $code;

    switch ($code) {
      case 503:
        $this->title   = "Service Unavailable";
        $this->message = $message;
        break;
    }

    Status::set($this->getStatusCode());
  }

  /**
   * Check if request is ajax or not
   *
   * @return bool
   */
  public function isAjax() : bool
  {
    return $this->isAjax;
  }

  /**
   * Return status code
   *
   * @return int
   */
  public function getStatusCode() : int
  {
    return $this->statusCode;
  }

  /**
   * Returns page title
   *
   * @return mixed
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * Check if application can render error
   *
   * @return void
   */
  private function render() : void
  {
    $this->handle();
  }

  /**
   * Handle the error
   *
   * @return void
   */
  public function handle() : void
  {
    Events::trigger('server.error', [$this]);
    exit;
  }

}
