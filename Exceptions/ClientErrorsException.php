<?php

namespace Modulus\Framework\Exceptions;

use Exception;
use Modulus\Http\Status;
use Modulus\Support\Config;
use Modulus\Utility\Events;

class ClientErrorsException extends Exception
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
  protected $statusCode = 404;

  /**
   * $title
   *
   * @var string
   */
  protected $title = 'Not found';

  /**
   * __construct
   *
   * @return void
   */
  public function __construct(bool $isAjax, int $code)
  {
    $this->isAjax     = $isAjax;
    $this->statusCode = $code;

    switch ($code) {
      case 403:
        $this->title   = "Access denied";
        $this->message = "You don't have authorization to view this page.";
        break;

      case 404:
        $this->title   = "Not found";
        $this->message = "Not Found!";
        break;

      case 429:
        $this->title   = "Too many requests";
        $this->message = "Sorry, you are making too many requests to our servers.";
        break;

      default:
        $this->title   = "Method not allowed";
        $this->message = "Bad request";
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
    Events::trigger('client.error', [$this]);
    exit;
  }

}
