<?php

namespace Modulus\Framework\Exceptions;

use Exception;

class TokenMismatchException extends Exception
{
  /**
   * __construct
   *
   * @param string $message
   * @return void
   */
  public function __construct(string $message)
  {
    $args = debug_backtrace();

    foreach (end($args) as $key => $value) {
      $this->{$key} = $value;
    }

    $this->message = $message;
  }
}
